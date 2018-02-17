<?php

namespace App\Conversations;

use App\Faq;
use App\FaqAction;
use App\FbUser;
use App\Http\Controllers\FlowRunsController;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class TestFollowup extends Conversation
{
    public $bot;
    public $fb_user;
    public function __construct(BotMan $bot){
        $this->bot = $bot;
    }
    /**
     * First question
     */
    public function startFollowup()
    {
        FlowRunsController::saveRun($this->bot,10);
        $user = $this->bot->getUser();
        $this->fb_user = FbUser::where('user_id',$user->getId())->first();
        $question = Question::create('Have You Used a Self Test Kit?')
            ->fallback('Unable to ask whether used kit')
            ->callbackId('have_used_kit')
            ->addButtons([
                Button::create('Yes')->value('YES_TEST'),
                Button::create('No')->value('NO_TEST'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $selectedText = $answer->getText();
                if ($selectedValue == 'YES_TEST'){
                    $this->fb_user->tested = 1;
                    $this->fb_user->save();
                }elseif($selectedValue == 'NO_TEST'){
                    $this->fb_user->tested = 0;
                    $this->fb_user->save();
                }else{
                    $this->startFollowup();
                }
            }else{
                $this->startFollowup();
            }
        });
    }

    public function startYesTest(){
        FlowRunsController::saveRun($this->bot,11);
        $question = Question::create('Was this your first time to use the Self Test Kit?')
            ->fallback('Unable to ask whether times of kit use')
            ->callbackId('first_time_user')
            ->addButtons([
                Button::create('Yes')->value('YES_FIRST_USER'),
                Button::create('No')->value('NO_FIRST_USER'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $selectedText = $answer->getText();
                if ($selectedValue == 'YES_FIRST_USER'){
                    $this->fb_user->first_user = 1;
                    $this->fb_user->save();
                }elseif($selectedValue == 'NO_FIRST_USER'){
                    $this->fb_user->first_user = 0;
                    $this->fb_user->save();
                }else{
                    $this->startYesTest();
                }
                //ask where kit was bought
                $this->askPlace();
            }else{
                $this->startYesTest();
            }
        });

    }

    public function startNoTest(){
        FlowRunsController::saveRun($this->bot,12);
        $this->say('if you need more information and support,');
        $main_menu = new AskAgeAndGender($this->bot);
        $main_menu->displayMainMenu();
    }

    public function askPlace(){
        $this->ask('Where Did you purchase the Self Test Kit?', function(Answer $answer) {
            // Save result
            $this->fb_user->bought_from = $answer->getText();
            $this->fb_user->save();
        });
    }

    public function askKitType(){
        $question = Question::create('Which of these Self Test Kits did you use?')
            ->fallback('Unable to ask kit type used')
            ->callbackId('kit_type_tested')
            ->addButtons([
                Button::create('Oral Kit (Oraquick) ')->value('ORAL_KIT'),
                Button::create('Blood Kit (Insti)')->value('BLOOD_KIT'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $selectedText = $answer->getText();
                if ($selectedValue == 'ORAL_KIT'){
                    $this->fb_user->kit_used = 'Oral';
                    $this->fb_user->save();
                }elseif($selectedValue == 'BLOOD_KIT'){
                    $this->fb_user->kit_used = 'Blood';
                    $this->fb_user->save();
                }else{
                    $this->askKitType();
                }
                //disclose results
                $this->startConversation(new ResultsDisclosure($this->bot));

            }else{
                $this->askKitType();
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->startFollowup();
    }
}
