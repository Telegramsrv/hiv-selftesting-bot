<?php

namespace App\Conversations;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Location;
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
            $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$this->lat.",".$this->lon."&sensor=true";

            $json = file_get_contents($url);
            $data = json_decode($json, true);

            $this->say('Received: '.print_r($data, true));
        }, null, [
            'message' => [
                'quick_replies' => json_encode([
                    [
                        'content_type' => 'location'
                    ]
                ])
            ]
        ]);

        /*$question = Question::create("Please Share your location or typein your county")
            ->callbackId('ask_location')
            ->addButtons([
                Button::create('Tell a joke')->value('joke'),
                Button::create('Give me a fancy quote')->value('quote'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'joke') {
                    $joke = json_decode(file_get_contents('http://api.icndb.com/jokes/random'));
                    $this->say($joke->value->joke);
                } else {
                    $this->say(Inspiring::quote());
                }
            }
        });*/
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askLocation();
    }
}
