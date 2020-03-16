<?php


namespace lbs\command\model;

use Illuminate\Database\Eloquent\Model;


class Client extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public $incrementing = true;
}