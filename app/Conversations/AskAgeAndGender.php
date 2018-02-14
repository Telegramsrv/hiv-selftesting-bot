<?php

namespace App\Conversations;

use App\FbUser;
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
            $this->ask('Please enter your age eg 24', function(Answer $answer) {
                // Save result
                $this->age =  $answer->getText();
                $this->askGender();
            });
        }
    }

    public function askGender(){
        $question = Question::create('Please select your gender')
            ->fallback('Unable to ask for gender')
            ->callbackId('ask_gender')
            ->addButtons([
                Button::create('Male')->value('M'),
                Button::create('Female')->value('F'),
                Button::create('Other')->value('O'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $selectedText = $answer->getText();
                $this->gender = $selectedValue;
                $this->saveAgeAndGender();
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

        /*$this->ask('Reply with 1 to get more information on HIV Self Testing.
        Reply with 2 for instructions to use a Self Test Kit.
        Reply with 3 for locations to buy a Self Kit.
         Reply with 4 to talk to a Counselor', function(Answer $answer) {
            // Save result
            $menu =  $answer->getText();
            //$this->askGender();
            if ($menu == 1){
                $this->bot->startConversation(new ShowFaqs($this->bot));
            }elseif($menu == 2){
                $this->bot->reply('We are Still Testing this feature...');
            }elseif($menu == 3){
                $this->bot->reply('We are Still Testing this feature...');
            }elseif($menu == 4){
                $this->bot->reply('We are Still Testing this feature...');
            }else{
                $this->say('Please select one of the options.');
            }
        });*/
        $this->bot->typesAndWaits(2);
        $this->bot->reply(ListTemplate::create()
            ->useCompactView()
            ->addElement(
                Element::create('More information on HIV Self Testing')
                    ->subtitle('FAQs and More')
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View')
                        ->payload('faqs_1')->type('postback'))
            )
            ->addElement(
                Element::create('How to use the Self Test Kit')
                    ->subtitle('Instructions')
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View')
                        ->payload('instructions_2')->type('postback')
                    )
            )
            ->addElement(
                Element::create('Where to Buy a Self Test Kit')
                    ->subtitle('Locations')
                    ->image('http://developers.tmcg.co.ug/images/positive.jpg')
                    ->addButton(ElementButton::create('View')
                        ->payload('locations_3')->type('postback')
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
