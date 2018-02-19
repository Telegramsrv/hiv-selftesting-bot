<?php

namespace App\Conversations;

use App\Http\Controllers\FlowRunsController;
use App\Location as DBLocation;
use App\Pharmacy;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
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

class ShowLocationsByCounty extends Conversation
{
    public $bot;
    protected $county;
    public function __construct(BotMan $bot){
        $this->bot = $bot;
    }
    /**
     * First question
     */
    public function askCounty()
    {
        FlowRunsController::saveRun($this->bot,8);
        /*$attachment = new Image('http://developers.tmcg.co.ug/images/positive.jpg', [
            'custom_payload' => true,
        ]);
        $message = OutgoingMessage::create('Use the image below and reply with the name of your county')
            ->withAttachment($attachment);*/
        //$this->say('Use the image below and reply with the name of your county');
        $this->ask('Please enter the name of your county', function(Answer $answer) {
            // Save result
            $this->county = $answer->getText();
            //get pharmacies in county
            $pharmacies = Pharmacy::where('county','like','%'.$this->county.'%')->take(4)->get();
            if (count($pharmacies)>0 && $pharmacies != null){
                $this->bot->reply('Here is a List of the nearest pharmacies where you can get an HIV self test kit in '.$this->county);
                $this->sendLocationsList($pharmacies);
            }else{
                $this->say($this->county.' County not Found!');
                $this->askCounty();
            }
        });
    }

    public function sendLocationsList($pharmacies){
        if (count($pharmacies) == 1){
            $this->bot->reply(ListTemplate::create()
                ->useCompactView()
                ->addElement($this->addPharmacy($pharmacies[0]))
            );
        }elseif (count($pharmacies) == 2){
            $this->bot->reply(ListTemplate::create()
                ->useCompactView()
                ->addElement($this->addPharmacy($pharmacies[0]))
                ->addElement($this->addPharmacy($pharmacies[1]))
            );
        }elseif (count($pharmacies) == 3){
            $this->bot->reply(ListTemplate::create()
                ->useCompactView()
                ->addElement($this->addPharmacy($pharmacies[0]))
                ->addElement($this->addPharmacy($pharmacies[1]))
                ->addElement($this->addPharmacy($pharmacies[2]))
            );
        }elseif (count($pharmacies) == 4){
            $this->bot->reply(ListTemplate::create()
                ->useCompactView()
                ->addElement($this->addPharmacy($pharmacies[0]))
                ->addElement($this->addPharmacy($pharmacies[1]))
                ->addElement($this->addPharmacy($pharmacies[2]))
                ->addElement($this->addPharmacy($pharmacies[3]))
            );
        }else{
            $this->askCounty();
        }
    }

    public function addPharmacy($pharmacy){
        $element = Element::create($pharmacy->name)
            ->subtitle($pharmacy->address.' - '.$pharmacy->county)
            ->image('http://developers.tmcg.co.ug/images/positive.jpg')
            ->addButton(ElementButton::create('View Map')->url('http://developers.tmcg.co.ug/location/'.$pharmacy->lat.'/'.$pharmacy->lon));
        return $element;
    }

    /**Element::create($pharmacies[3]->name)
                    ->subtitle($pharmacies[3]->address.' - '.$pharmacies[3]->county)
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View Map')->url('http://developers.tmcg.co.ug/location/'.$pharmacies[3]->lat.'/'.$pharmacies[3]->lon))
     * Start the conversation
     */
    public function run()
    {
        $this->askCounty();
    }
}
