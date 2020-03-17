<?php


namespace geoquizz\app\model;


use Illuminate\Database\Eloquent\Relations\Pivot;

class Photo_Serie extends Pivot
{
    protected $table ='photo_serie';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function photo(){
        return $this->belongsTo('geoquizz\app\model\Photo','id_photo');
    }

    public function serie(){
        return $this->belongsTo('geoquizz\app\model\Serie','id_serie');
    }
}