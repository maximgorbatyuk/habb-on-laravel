<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\MiscUtils;
use App\Models\Gamer;
use App\Models\GamerScore;
use App\Traits\GamerConstructor;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Log;
use Redirect;
use Validator;

class GamerController extends Controller
{
    use GamerConstructor;

    public function index(Request $request)
    {
        return view('admin.gamers.index');
    }

    public function gamerReportForDatatable(Request $request){

        $columns = [ "id", "name", "phone", "vk_page", "primary_game", "source"];

        $totalData = Gamer::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $gamers = Gamer::offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $filtered = Gamer::where('id','LIKE',"%{$search}%")
                ->orWhere('name', 'LIKE',"%{$search}%")
                ->orWhere('last_name', 'LIKE',"%{$search}%")
                ->orWhere('phone', 'LIKE',"%{$search}%");

            $gamers =  $filtered
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = $filtered->count();
        }
        /** @var Gamer[] $gamers */
        //----------
        $data = [];
        if(!empty($gamers))
        {
            foreach ($gamers as $gamer)
            {
                if(is_null($gamer->external_service_id))
                {
                    $source = "Сайт HABB";
                }
                else
                {
                    $externalService = $gamer->externalService;
                    $link = action('ExternalServicesController@show', ['id' => $externalService->id]);
                    $source = "<a href='{$link}' title='SHOW'>{$externalService->title}</a>";
                }
                $show =  action('GamerController@show',$gamer->id);
                $nestedData['id'] = $gamer->id;
                $nestedData['name'] = "<a href='{$show}' title='Открыть в новой вкладке' target='_blank' >{$gamer->getFullName()}</a>" ;
                $nestedData['phone'] = $gamer->phone;
                $nestedData['vk_page'] = $gamer->vk_page;
                $nestedData['primary_game'] = $gamer->primary_game;
                $nestedData['source'] = $source;

                $data[] = $nestedData;

            }
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];

        return response()->json($json_data);
    }

    #region Ресурсные методы


    public function create()
    {
        $userAgent = request()->header('User-Agent');
        $isIosDevice = stripos($userAgent,"iPod")||stripos($userAgent,"iPhone")||stripos($userAgent,"iPad");

        return view('admin.gamers.create');
    }

    public function store(Request $request)
    {
        $input = $request->input();
        $validator = Validator::make($input, Gamer::$rules);

        if ($validator->fails()) {
            return Redirect::action('GamerController@create')
                ->withErrors($validator->errors())
                ->withInput($input);
        }

        $gamer = $this->constructGamerInstance(Input::all());

        $success = $gamer->save();
        if ($success == false) {
            return Redirect::action('GamerController@create')
                ->withErrors($gamer->errors())
                ->withInput($input);

        }
        $scores = GamerScore::getScoreSet();
        $gamer->scores()->saveMany($scores);

        return Redirect::action('GamerController@show', ["id" => $gamer->id])
            ->with('success', 'Данные сохранены');
    }


    public function show($id)
    {
        /** @var Gamer $gamer */
        $gamer = Gamer::find($id);
        $model = new \App\ViewModels\Back\GamerShowViewModel();
        $model->gamer = $gamer;
        $model->scores = $gamer->getScores();
        return view('admin.gamers.show', [ 'model' => $model]);
    }

    public function edit($id)
    {
        /** @var Gamer $gamer */
        $gamer = Gamer::find($id);
        $model = new \App\ViewModels\Back\GamerShowViewModel();
        $model->gamer = $gamer;
        $model->scores = $gamer->scores;
        return view('admin.gamers.edit', ['model' => $model]);
    }

    public function update(Request $request, $id)
    {
        /** @var Gamer $gamer */
        $gamer = $this->constructGamerInstance(Input::all(), $id);
        $res = $gamer->updateUniques();

        if ($res === false) {
            return Redirect::action('GamerController@edit', ["id" => $gamer->id])
                ->withErrors($gamer->errors())
                ->withInput(Input::all());
        }
        return Redirect::action('GamerController@show', ["id" => $gamer->id])
            ->with('success', 'Данные сохранены');
    }



    public function destroy($id)
    {
        /** @var Gamer $gamer */
        $gamer = Gamer::find($id);
        $gamer->deleted_at = Carbon::now();
        $result = $gamer->save();

        if ($result == true) {
            $message = "Запись ID".$gamer->id." удалена из базы";
            $type = Constants::Success;
        } else {
            $message = "Запись ID".$gamer->id." не удалена из базы<br>";
            $errors = $gamer->errors();
            foreach ($errors as $error) {
                $message .= $error."<br>";
            }
            $type = Constants::Error;
        }
        flash($message, $type);
        return Redirect::action('GamerController@index');
    }

    #endregion

    /**
     * Обновляет очки геймера
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function scoreUpdate(Request $request)
    {
        /** @var Gamer $gamer */
        $id = Input::get('gamer_id');
        $gamer = Gamer::with('scores')->find($id);
        $gameName = Input::get('game_name');
        $scoreValue = Input::get('score_value');

        if (is_null($gameName) || is_null($scoreValue)) {
            Log::info("Прислан невалидный реквест на обновление очков команды");

            flash("Название игры или очки пустые", Constants::Error);
            return Redirect::action('GamerController@show', ["id" => $id]);
        }


        $result = $gamer->addScoreValue($gameName, $scoreValue);
        if ($result == false) {

            $message = "Не найдена запись очков игрока<br>";
            $message .= join('<br>', $gamer->errors());
            flash($message, Constants::Error);
            return Redirect::action('GamerController@show', ["id" => $id]);
        }
        flash("Очки обновлены", Constants::Success);
        return Redirect::action('GamerController@show', ["id" => $id]);
    }

    #region Форма регистрации участника HABB
    /**
     * Открытие формы регистрации
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function registerForm(Request $request) {

        $userAgent = $request->header('User-Agent');
        $iOsDevice = stripos($userAgent,"iPod")||
            stripos($userAgent,"iPhone") ||
            stripos($userAgent,"iPad");

        $cities = Constants::getCities();
        return view('front.register.gamer', ['iOsDevice' => $iOsDevice, 'cities' => $cities]);
    }



    /**
     * Принимает аргументы от формы регистрации и создает аккаунт игрока
     * @param Request $request
     * @return GamerController|\Illuminate\Http\RedirectResponse
     */
    public function createGamerAccount(Request $request){
        // TODO Нужно проверить как работает валидация
        $input = $request->input();
        $validator = Validator::make($input, Gamer::$rules);

        if ($validator->fails()) {
            return Redirect::action('GamerController@registerForm')
                ->withErrors($validator->errors())
                ->withInput($input);
        }

        $gamer = $this->constructGamerInstance(Input::all());

        $success = $gamer->save();
        if ($success == false) {
            return Redirect::action('GamerController@registerForm')
                ->withErrors($gamer->errors())
                ->withInput($input);

        }
        $scores = GamerScore::getScoreSet();
        $gamer->scores()->saveMany($scores);

        session(['gamer_id' => $gamer->id]);
        return Redirect::action('GamerController@displayGamerRegisterResult');
    }

    public function displayGamerRegisterResult(Request $request){
        $id = $request->session()->get('gamer_id');

        if (is_null($id)) {
            // Если внезапно id нет, то редирект на страницу регистрации участника
            return Redirect::action('GamerController@registerForm');
        }
        $gamer = Gamer::find($id);
        $request->session()->forget('gamer_id');
        return view('front.register.gamer-result', ['gamer' => $gamer]);
    }

    #endregion

    /**
     * Аякс запрос на поиск аккаунта по телефону
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchGamerForDuplicate(Request $request) {

        $field = Input::get('field');
        $searchable = Input::get('value');

        if ($field == 'phone') {
            $searchable = MiscUtils::formatPhone($searchable);
        }

        $gamer = DB::table('gamers')->where($field , '=', $searchable)->first();

        return response()->json([
            'result' => true,
            'exists' => !is_null($gamer)
        ]);
    }




}
