<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //Table Name
    protected $table = 'locations';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = true;
    public $lat;
    public $lon;


    //
    function distance($lat2, $lon2, $unit='K') {

        $theta = $this->lon - $lon2;
        $dist = sin(deg2rad($this->lat)) * sin(deg2rad($lat2)) +  cos(deg2rad($this->lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}
