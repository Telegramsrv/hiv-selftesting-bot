<?php

namespace App\Conversations;

use App\Http\Controllers\FlowRunsController;
use App\RapidproServer;
use BotMan\BotMan\BotMan;
use DateTime;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class TalkToCounselor extends Conversation
{
    public $bot;
    protected $quest;
    public function __construct(BotMan $bot){
        $this->bot = $bot;
    }
    /**
     * First question
     */
    public function sendContact()
    {
        FlowRunsController::saveRun($this->bot,5);
        $this->bot->reply('If you need to talk to a counselor regarding HIV self testing , you can call the toll free line - 1190');
        $this->say('You can also send a message below and you will get a reply from a counselor.');
        $question = Question::create('You can choose one of these actions')
            ->fallback('Unable to ask whether ask question')
            ->callbackId('should_ask_question')
            ->addButtons([
                Button::create('Ask Question')->value('ask_qn'),
                Button::create('Go To Main Menu')->value('main_menu'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $selectedText = $answer->getText();
                if ($selectedValue == 'ask_qn'){
                    $this->ask('Please go ahead and ask your question.', function(Answer $answer) {
                        $this->quest = $answer->getText();
                        $qn = new \App\Question;
                        $user = $this->bot->getUser();
                        $qn->psid = $user->getId();
                        $qn->body = $this->quest;
                        $qn->save();
                        $this->sendConversationRapidpro($user->getId(),$answer->getText());
                        $this->say('Your question has been received. You will get a reply soon');
                    });
                }elseif($selectedValue == 'main_menu'){
                    $askAgeGender = new AskAgeAndGender($this->bot);
                    $askAgeGender->displayMainMenu();
                }else{
                    $this->sendContact();
                }
            }else{
                $this->sendContact();
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->sendContact();
    }

    public function sendConversationRapidpro($userPsid, $message )
    {

        $serverDetails = RapidproServer::where("Status", 1)->first();
        $date = explode("+",date("c"));
        $data = array("from"=> $userPsid, "text"=> $message, "date"=> $date[0].'.034');
        $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                        ));
        $context  = stream_context_create($options);
        $result = file_get_contents($serverDetails->Url, false, $context);

    }
}
