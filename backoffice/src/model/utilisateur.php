<?php


namespace geoquizz\app\model;


use Illuminate\Database\Eloquent\Model;

class utilisateur extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = true;
}