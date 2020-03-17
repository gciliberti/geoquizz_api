<?php


namespace geoquizz\app\model;


use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $table = 'serie';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    public function photos(){
        return $this->belongsToMany('geoquizz\app\model\Photo')
            ->using('geoquizz\app\model\Photo_Serie');
    }
}