<?php


namespace geoquizz\app\model;

use Illuminate\Database\Eloquent\Model;


class Partie extends Model
{
    protected $table = 'partie';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = true;


}