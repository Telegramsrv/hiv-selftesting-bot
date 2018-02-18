<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function show($lat, $lng){

        return view('web-views.location')->with([
                'lat' => $lat,
                'lng' => $lng
        ]);
    }
}
