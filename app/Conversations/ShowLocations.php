<?php

namespace App\Conversations;

use App\Http\Controllers\FlowRunsController;
use App\Location as DBLocation;
use App\Pharmacy;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;
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
        FlowRunsController::saveRun($this->bot,7);
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
            //get first 3 locations
            $pharmacies = array();
            $i=0;
            foreach ($distances as $index => $distance){
                $pharma = Pharmacy::find($index);
                if ($pharma != null){
                    array_push($pharmacies, $pharma);
                }else{
                    continue;
                }

                $i++;
                if ($i>3){
                    break;
                }
            }
            $this->bot->reply('Here is a List of the nearest pharmacies where you can get an HIV self test kit');
            $this->sendLocationsList($pharmacies);

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

    public function sendLocationsList($pharmacies){
        $this->bot->reply(ListTemplate::create()
            ->useCompactView()
            ->addElement(
                Element::create($pharmacies[0]->name)
                    ->subtitle($pharmacies[0]->address.' - '.$pharmacies[0]->county)
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View Map')->url('http://developers.tmcg.co.ug/location/'.$pharmacies[0]->lat.'/'.$pharmacies[0]->lon))
            )
            ->addElement(
                Element::create($pharmacies[1]->name)
                    ->subtitle($pharmacies[1]->address.' - '.$pharmacies[1]->county)
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View Map')->url('http://developers.tmcg.co.ug/location/'.$pharmacies[1]->lat.'/'.$pharmacies[1]->lon))
            )
            ->addElement(
                Element::create($pharmacies[2]->name)
                    ->subtitle($pharmacies[2]->address.' - '.$pharmacies[2]->county)
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View Map')->url('http://developers.tmcg.co.ug/location/'.$pharmacies[2]->lat.'/'.$pharmacies[2]->lon))
            )->addElement(
                Element::create($pharmacies[3]->name)
                    ->subtitle($pharmacies[3]->address.' - '.$pharmacies[3]->county)
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View Map')->url('http://developers.tmcg.co.ug/location/'.$pharmacies[3]->lat.'/'.$pharmacies[3]->lon))
            )
        );
    }
    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askLocation();
    }
}
