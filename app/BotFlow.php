<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BotFlow extends Model
{
    //Table Name
    protected $table = 'bot_flows';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = false;
}
