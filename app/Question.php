<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //Table Name
    protected $table = 'questions';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = true;
}
