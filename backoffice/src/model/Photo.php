<?php


namespace geoquizz\app\model;

use Illuminate\Database\Eloquent\Model;


class Photo extends Model
{
    protected $table = 'photo';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = true;
}