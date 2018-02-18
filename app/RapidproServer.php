<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RapidproServer extends Model
{
    //Table Name
    protected $table = 'rapidpro_servers';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = true;
}
