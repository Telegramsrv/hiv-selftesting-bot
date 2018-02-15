<?php

namespace App\Conversations;

use BotMan\BotMan\BotMan;
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
        $this->bot->reply('Contact 1190 toll free or visit www.besure.co.ke for more information');
        $this->ask('You can ask a question and a counselor will send you a reply. Ask Question', function(Answer $answer) {
            $this->quest = $answer->getText();
            $this->say('Your question has been received. You will get a reply seen');
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->sendContact();
    }
}
