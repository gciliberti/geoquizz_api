<?php


namespace geoquizz\app\model;


use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema()
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
     */
    protected $table = 'serie';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    public function photos(){
        return $this->belongsToMany('geoquizz\app\model\Photo')
            ->using('geoquizz\app\model\Photo_Serie');
    }
}