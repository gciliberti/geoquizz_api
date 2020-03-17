<?php


namespace geoquizz\app\model;

use Illuminate\Database\Eloquent\Model;


class Serie extends Model
{
    protected $table = 'serie';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;
}