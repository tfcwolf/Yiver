<?php
namespace App\model;
use \ActiveRecord;
class User extends ActiveRecord{
    public $table = '{{user}}';
    public $primaryKey = 'uid';
}