<?php


namespace geoquizz\app\model;


use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $table = 'map';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = true;
}