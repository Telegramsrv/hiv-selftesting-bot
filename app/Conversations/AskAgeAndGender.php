<?php

namespace App\Conversations;

use App\FbUser;
use App\FlowRun;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class AskAgeAndGender extends Conversation
{
    public $bot;
    protected $age;
    protected $gender;

    public function __construct(BotMan $bot){
        $this->bot = $bot;
    }

    /**
     * First question
     */
    public function askAge()
    {
        if (FbUser::where('user_id',$this->bot->getUser()->getId())->whereNotNull('age')->first() != null){
            $this->displayMainMenu();
        }else{
            $this->bot->typesAndWaits(2);
            $this->ask('Please enter your age eg 24', function(Answer $answer) {
                // Save result
                $this->age =  $answer->getText();
                if ($this->age < 10){
                    $this->askAge();
                }else{
                    $this->askGender();
                }
            });
        }
    }

    public function askGender(){
        $question = Question::create('Please select your gender')
            ->fallback('Unable to ask for gender')
            ->callbackId('ask_gender')
            ->addButtons([
                Button::create('Male')->value('Male'),
                Button::create('Female')->value('Female'),
                Button::create('Other')->value('Other'),
            ]);
        $this->bot->typesAndWaits(2);
        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $selectedText = $answer->getText();
                $this->gender = $selectedValue;
                $this->saveAgeAndGender();
            }else{
                $this->askGender();
            }
        });
    }

    public function saveAgeAndGender(){
            $user = $this->bot->getUser();
        // Access user psid (page scoped id)
        $psid = $user->getId();
        $fb_user = FbUser::where('user_id', $psid)->first();
        $fb_user->age = $this->age;
        $fb_user->user_gender = $this->gender;
        $fb_user->save();
        $this->displayMainMenu();
    }

    public function displayMainMenu(){
        $this->bot->typesAndWaits(2);
        $this->say(' Please choose what kind of information you need from the menu below.');
        $this->bot->typesAndWaits(2);
        $this->bot->reply(ListTemplate::create()
            ->useCompactView()
            ->addElement(
                Element::create('General Information on HIV Self Testing')
                    ->subtitle('Frequently Asked Questions (FAQs)')
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View')
                        ->payload('faqs_1')->type('postback'))
            )
            ->addElement(
                Element::create(' Where to Buy/Find an HIV Self Test kit')
                    ->subtitle('Pharmacies & Locations')
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View')
                        ->payload('locations_3')->type('postback')
                    )
            )
            ->addElement(
                Element::create('How to use the HIV Self Test Kit')
                    ->subtitle('Instructions & Videos')
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View')
                        ->payload('instructions_2')->type('postback')
                    )
            )
            ->addElement(
                Element::create('Talk to a Counselor')
                    ->subtitle('You can contact our counselors')
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('Start')
                        ->payload('counselors_4')->type('postback')
                    )
            )
        );
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askAge();
    }
}
