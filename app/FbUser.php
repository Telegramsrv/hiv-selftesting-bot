<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FbUser extends Model
{
    //Table Name
    protected $table = 'fb_users';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = true;
}
