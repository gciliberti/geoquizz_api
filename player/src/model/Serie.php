<?php


namespace geoquizz\app\model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(type="object",required={ "series"})
 */
class Serie extends Model
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="string",
     *     description="id de l'event",
     *     example="bf9drea3-35c6-34f3-bc09-fd085fb34119"
     * )
     *
     */

    use SoftDeletes;

    protected $hidden = ["deleted_at"];

    protected $table = 'serie';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    public function photos(){
        return $this->belongsToMany('geoquizz\app\model\Photo')
            ->using('geoquizz\app\model\Photo_Serie');
    }

    public function map(){
        return $this->hasOne('geoquizz\app\model\Map','id','map_refs');
    }
}