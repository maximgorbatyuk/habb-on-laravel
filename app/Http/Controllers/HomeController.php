<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\FrontDataFiller;
use App\Helpers\MiscUtils;
use App\Helpers\VarDumper;
use App\Models\Banner;
use App\Models\GamerTournamentEventGuest;
use App\Models\Post;
use App\Models\StaticPage;
use App\Models\Tournament;
use App\ViewModels\Front\Auth\ProfileFormViewModel;
use App\ViewModels\Front\Home\AboutHomeViewModel;
use App\ViewModels\Front\Home\ContactHomeViewModel;
use App\ViewModels\Front\Home\StaticPageFrontViewModel;
use App\ViewModels\Front\HomePageViewModel;
use App\ViewModels\Front\ShowPostViewModel;
use App\ViewModels\Front\TeamCreateRequest\RegisterTeamFormViewModel;
use App\ViewModels\Front\TournamentViewModel;
use App\ViewModels\NewsViewModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $model = new HomePageViewModel;

        $model->posts = Post::getTop(HomePageViewModel::TopPostCount);
        $model->posts_count = count($model->posts);


        $model->banners = Banner::getForMainPage();
        $model->banners_count = count($model->banners);

        FrontDataFiller::create($model)->fill();

        return view('front.home.index', ['model' => $model]);
    }



    public function about() {

        /** @var StaticPage $eventSchedule */
        $eventSchedule = StaticPage::getByUniqueName(StaticPage::AboutUsPage_RowName);

        $eventSchedule->decodeHtmlContent();

        $model = new StaticPageFrontViewModel();
        $model->pageTitle = $eventSchedule->title;
        $model->staticPage = $eventSchedule;

        FrontDataFiller::create($model)->fill();

        return view('front.home.static_page', ["model" => $model]);
    }



    public function contacts() {

        $model = new ContactHomeViewModel();
        FrontDataFiller::create($model)->fill();

        return view('front.home.contacts', ['model' => $model]);
    }


    public function news(Request $request) {

        $hashtagFilter = $request->query('hashtag');
        $hasHashtag = isset($hashtagFilter);

        $posts = $hasHashtag ? Post::searchByHashtags($hashtagFilter) : Post::getAllActiveForFront();

        foreach ($posts as $post) {
            $post->decodeHtmlContent();
        }

        $model = new NewsViewModel($posts);
        $model->pageTitle = $hasHashtag ? "Поиск новостей по тегу #{$hashtagFilter}:" : "Новости киберсопрта";

        FrontDataFiller::create($model)->fill();

        return view('front.posts.index', ["model" => $model]);
    }


    public function profile(Request $request){
        $currentUser = \Auth::user();
        if (is_null($currentUser)) {

            flash('Пользователь не авторизован', Constants::Warning);
            return \Redirect::to('/');
        }

        $model = new ProfileFormViewModel;
        $model->current_user = $currentUser;
        FrontDataFiller::create($model)->fill();

        return view('auth.profile', ['model' => $model]);
    }

    public function openPost($id){
        /** @var Post $post */
        $post = Post::find($id);

        $topPosts = Post::getTop(3, $id);

        $user = \Auth::user();
        if (\Auth::guest() || !$user->hasBackendRight()) {
            $post->views = $post->views+1;
            $post->save();
        }
        $post->decodeHtmlContent();

        $model = new ShowPostViewModel();
        $model->post = $post;
        $model->topPosts = $topPosts;
        $model->hasAnotherPosts = count($topPosts) > 0;

        FrontDataFiller::create($model)->fill();

        return view('front.posts.show', ["model" => $model]);
    }


    public function openTournament($id, $sharedByHabbId = null) {

        /** @var Tournament $tournament */
        $tournament = Tournament::find($id);

        $tournament->decodeHtmlDescription();

        $topNews = Post::searchByHashtags($tournament->getHashtagsAsArray(), 3);

        $model = new TournamentViewModel();
        $model->sharedByHabbId = $sharedByHabbId;
        $model->tournament = $tournament;
        $model->topNews = $topNews;

        $model->banners = $tournament->banners()->get();
        $model->banners_count = count($model->banners);

        $model->showRegisterForTournamentButton = $tournament->registration_deadline->gt(MiscUtils::getLocalDatetimeNow());
        $model->showRegisterForEventButton = $tournament->event_date->gt(MiscUtils::getLocalDatetimeNow());

        $model->registrationDeadlineString = $tournament->RegistrationDeadline();
        $model->eventDateString = $tournament->EventDate();

        FrontDataFiller::create($model)->fill();

        return view('front.tournaments.show', ["model" => $model]);
    }

    public function eventSchedule(){

        /** @var StaticPage $eventSchedule */
        $eventSchedule = StaticPage::getByUniqueName(StaticPage::EventSchedule_RowName);

        $eventSchedule->decodeHtmlContent();

        $model = new StaticPageFrontViewModel();
        $model->pageTitle = $eventSchedule->title;
        $model->staticPage = $eventSchedule;

        FrontDataFiller::create($model)->fill();

        return view('front.home.static_page', ["model" => $model]);
    }
}
