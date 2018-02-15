<?php

namespace App\Conversations;

use App\Location as DBLocation;
use App\Pharmacy;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ShowLocations extends Conversation
{
    public $bot;
    protected $lat;
    protected $lon;
    public function __construct(BotMan $bot){
        $this->bot = $bot;
    }
    /**
     * First question
     */
    public function askLocation()
    {
        $this->askForLocation('Please share your location:', function (Location $location) {
            $this->lat = $location->getLatitude();
            $this->lon = $location->getLongitude();
            /*$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$this->lat.",".$this->lon."&sensor=true";
            $json = file_get_contents($url);
            $data = json_decode($json, true);*/
            //get
            $distances = array();
            $pharmas = Pharmacy::all();
            foreach ($pharmas as $index => $pharma){
                $distances[$pharma->id] = DBLocation::distance($pharma->lat, $pharma->lon, $this->lat,$this->lon);
            }
            //sort asc
            arsort($distances);
            //get first location
            foreach ($distances as $index => $distance){
                $pharma = Pharmacy::find($index);
                break;
            }
            $this->bot->reply($pharma->name);
            $this->bot->reply($pharma->address);
            //
            $this->bot->reply(ButtonTemplate::create('View place on a map')
                ->addButton(ElementButton::create('View Map')->url('http://developers.tmcg.co.ug/'.$pharma->lat.'/'.$pharma->lon))
            );

        }, null, [
            'message' => [
                'quick_replies' => json_encode([
                    [
                        'content_type' => 'location'
                    ]
                ])
            ]
        ]);

    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askLocation();
    }
}
