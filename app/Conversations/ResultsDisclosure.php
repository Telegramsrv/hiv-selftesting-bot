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

class ResultsDisclosure extends Conversation
{
    public $bot;
    public $fb_user;
    public function __construct(BotMan $bot){
        $this->bot = $bot;
    }
    /**
     * First question
     */
    public function disclose()
    {
        FlowRunsController::saveRun($this->bot,13);
        $user = $this->bot->getUser();
        $this->fb_user = FbUser::where('user_id',$user->getId())->first();
        $question = Question::create('How did the results come out from the Self Test Kit?')
            ->fallback('Unable to ask test results')
            ->callbackId('ask_test_results')
            ->addButtons([
                Button::create('Positive')->value('POSITIVE'),
                Button::create('Negative')->value('NEGATIVE'),
                Button::create('Unclear')->value('UNCLEAR'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $selectedText = $answer->getText();
                if ($selectedValue == 'POSITIVE'){
                    $this->fb_user->results = 'Positive';
                    $this->fb_user->save();
                    $this->say('What to do with Positive Results .....');
                    $this->startConversation(new TalkToCounselor($this->bot));
                    $this->say('What to do with Positive Results .....');
                }elseif($selectedValue == 'NEGATIVE'){
                    $this->fb_user->results = 'Negative';
                    $this->fb_user->save();
                    $this->startConversation(new TalkToCounselor($this->bot));
                }elseif($selectedValue == 'UNCLEAR'){
                    $this->fb_user->results = 'Unclear';
                    $this->fb_user->save();
                    $this->say('What to do with Positive Results .....');
                    $this->startConversation(new TalkToCounselor($this->bot));
                }else{
                    $this->disclose();
                }
            }else{
                $this->disclose();
            }
        });
    }


    /**
     * Start the conversation
     */
    public function run()
    {
        $this->disclose();
    }
}
