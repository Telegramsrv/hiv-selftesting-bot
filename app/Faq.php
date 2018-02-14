<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    //Table Name
    protected $table = 'faqs';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = false;
}
