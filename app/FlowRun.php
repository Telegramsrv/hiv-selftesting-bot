<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowRun extends Model
{
    //Table Name
    protected $table = 'flow_runs';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = true;
}
