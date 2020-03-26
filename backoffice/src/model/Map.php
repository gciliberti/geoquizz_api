<?php


namespace geoquizz\app\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Map extends Model
{
    use SoftDeletes;
    protected $table = 'map';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = true;
}