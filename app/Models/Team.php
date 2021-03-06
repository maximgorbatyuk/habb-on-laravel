<?php

namespace App\Models;

use App\Interfaces\ISelectableOption;
use App\Interfaces\ITournamentParticipant;
use App\Traits\TimestampModelTrait;
use Carbon\Carbon;
use DB;
use LaravelArdent\Ardent\Ardent;

/**
 * Class Team
 * @package App
 *
 * @property int id
 * @property string name
 * @property string city
 * @property string comment
 *
 * @property int captain_gamer_id
 * @property Gamer captain
 *
 * @property int second_gamer_id
 * @property Gamer secondGamer
 *
 * @property int third_gamer_id
 * @property Gamer thirdGamer
 *
 * @property int forth_gamer_id
 * @property Gamer forthGamer
 *
 * @property int fifth_gamer_id
 * @property Gamer fifthGamer
 *
 * @property int optional_gamer_id
 * @property Gamer optionalGamer
 *
 * @property Tournament[] tournamentsThatTakePart
 *
 * @property Carbon deleted_at
 * @property Carbon updated_at
 * @property Carbon created_at
 */
class Team extends Ardent implements ISelectableOption, ITournamentParticipant
{
    use TimestampModelTrait;

    const Captain_ForeignColumn         = "captain_gamer_id";
    const Captain_Field                 = "captain";

    const SecondGamer_ForeignColumn     = "second_gamer_id";
    const SecondGamer_Field             = "second_gamer";

    const ThirdGamer_ForeignColumn      = "third_gamer_id";
    const ThirdGamer_Field              = "third_gamer";

    const ForthGamer_ForeignColumn      = "forth_gamer_id";
    const ForthGamer_Field              = "forth_gamer";

    const FifthGamer_ForeignColumn      = "fifth_gamer_id";
    const FifthGamer_Field              = "fifth_gamer";

    const OptionalGamer_ForeignColumn   = "optional_gamer_id";
    const OptionalGamer_Field           = "optional_gamer";

    const Gamer_ModelName = "App\Models\Gamer";

    const TeamTournamentParticipants_ManyToManyTableName = "team_tournament_participants";

    const TableName = 'teams';
    protected $table = self::TableName;

    protected $fillable = array(
        'name',
        'city',
        'comment',
        self::Captain_ForeignColumn,
        self::SecondGamer_ForeignColumn,
        self::ThirdGamer_ForeignColumn,
        self::ForthGamer_ForeignColumn,
        self::FifthGamer_ForeignColumn ,
        self::OptionalGamer_ForeignColumn,
    );

    public static $rules = [
        'name'                          => 'required|between:1,100',
        'city'                          => 'required',
        self::Captain_ForeignColumn     => 'required',
        self::SecondGamer_ForeignColumn => 'required',
        self::ThirdGamer_ForeignColumn  => 'required',
        self::ForthGamer_ForeignColumn  => 'required',
        self::FifthGamer_ForeignColumn  => 'required',
    ];

    protected $dates = [
        'deleted_at'
    ];

    public static $relationsData = [
        self::Captain_Field         => [self::HAS_ONE, Gamer::class],
        self::SecondGamer_Field     => [self::HAS_ONE, Gamer::class],
        self::ThirdGamer_Field      => [self::HAS_ONE, Gamer::class],
        self::ForthGamer_Field      => [self::HAS_ONE, Gamer::class],
        self::FifthGamer_Field      => [self::HAS_ONE, Gamer::class],
        self::OptionalGamer_Field   => [self::HAS_ONE, Gamer::class],

        // ключ должен называться как называается имя метода связи
        'tournamentsThatTakePart'           => [self::BELONGS_TO_MANY, Tournament::class, 'table' => self::TeamTournamentParticipants_ManyToManyTableName]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Gamer
     */
    public function captain()
    {
        return $this->hasOne(self::Gamer_ModelName, 'id', self::Captain_ForeignColumn );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Gamer
     */
    public function secondGamer()
    {
        return $this->hasOne(self::Gamer_ModelName, 'id', self::SecondGamer_ForeignColumn );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Gamer
     */
    public function thirdGamer()
    {
        return $this->hasOne(self::Gamer_ModelName, 'id', self::ThirdGamer_ForeignColumn );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Gamer
     */
    public function forthGamer()
    {
        return $this->hasOne(self::Gamer_ModelName, 'id', self::ForthGamer_ForeignColumn);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Gamer
     */
    public function fifthGamer()
    {
        return $this->hasOne(self::Gamer_ModelName, 'id', self::FifthGamer_ForeignColumn );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Gamer
     */
    public function optionalGamer()
    {
        return $this->hasOne(self::Gamer_ModelName, 'id', self::OptionalGamer_ForeignColumn);
    }

    public function tournamentsThatTakePart(){
        return $this->belongsToMany(Tournament::class, self::TeamTournamentParticipants_ManyToManyTableName);
    }

    public function tryToAttachToTournamentAsParticipant($tournamentId){

        $currentTournamentIds = $this->tournamentsIdsThatTakePart();

        $currentTournamentIds[] = $tournamentId;

        $currentTournamentIds = collect($currentTournamentIds)->unique()->values();

        $this->tournamentsThatTakePart()->sync($currentTournamentIds);
    }

    /**
     * @return array
     */
    public function tournamentsIdsThatTakePart(){
        $tournaments = $this->tournamentsThatTakePart;
        $ids = [];
        foreach ($tournaments as $tournament)
            $ids[] = $tournament->id;

        return $ids;
    }

    public static function findTeamByParticipants($captainId, $secondId, $thirdId, $forthId, $fifthId) {

        $values = [$captainId, $secondId, $thirdId, $forthId, $fifthId];
        $item = self::query()
            ->whereIn(self::Captain_ForeignColumn, $values)
            ->whereIn(self::SecondGamer_ForeignColumn, $values)
            ->whereIn(self::ThirdGamer_ForeignColumn, $values)
            ->whereIn(self::ForthGamer_ForeignColumn, $values)
            ->whereIn(self::FifthGamer_ForeignColumn, $values)
            //->select()
            ->first();

        return $item;
    }

    /**
     * @param int $gamerId HABB ID игрока
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getTeamsWhereGamerTakeApart($gamerId) {

        return DB::table(self::TableName)
            ->where(self::Captain_ForeignColumn, '=', $gamerId)
            ->orWhere(self::SecondGamer_ForeignColumn, '=', $gamerId)
            ->orWhere(self::ThirdGamer_ForeignColumn, '=', $gamerId)
            ->orWhere(self::ForthGamer_ForeignColumn, '=', $gamerId)
            ->orWhere(self::FifthGamer_ForeignColumn, '=', $gamerId)
            ->select();
    }

    #region Interfaces

    public function getIdentifier()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name." [ID ".$this->id."]";
    }

    public function getClass()
    {
        return strtolower(class_basename($this));
    }

    public static function getSelectableOptionArray($withEmpty = true)
    {
        /** @var Team[] $gamers */
        $gamers = self::all();
        $gamerOptionList = [];

        if ($withEmpty == true) {
            $gamerOptionList[''] = 'Выберите участника';
        }
        foreach ($gamers as $gamer) {
            $gamerOptionList[$gamer->getIdentifier()] = $gamer->getName();
        }
        return $gamerOptionList;
    }

    #endregion
}
