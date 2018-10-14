<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 10/14/18
 * Time: 8:34 AM
 */

namespace App\Models;


use App\Traits\IHasHtmlContentTrait;
use App\Traits\TimestampModelTrait;
use Carbon\Carbon;
use Html;
use LaravelArdent\Ardent\Ardent;

/**
 * Class StaticPage
 *
 * @property int $id
 * @property string $unique_name
 * @property string $title
 * @property string $content
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class StaticPage extends Ardent
{
    const EventSchedule_RowName = "event_schedule";

    use TimestampModelTrait, IHasHtmlContentTrait;

    public static $rules = [
        'unique_name'   => 'required|max:100|unique:static_pages',
        'title'         => 'required|max:100',
        'content'       => 'between:0,2000',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $table = "static_pages";

    /** @var string $uniqueName
     * @return StaticPage
     */
    public static function getByUniqueName($uniqueName){

        return self::query()
            ->where('unique_name', '=', $uniqueName)
            ->firstOrFail();
    }
}