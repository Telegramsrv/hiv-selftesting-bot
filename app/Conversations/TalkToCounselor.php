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
        $this->bot->reply('Contact 1190 toll free or visit www.besure.co.ke for more information');
        $this->ask('You can ask a question and a counselor will send you a reply. Ask Question', function(Answer $answer) {
            $this->quest = $answer->getText();
            $qn = new \App\Question;
            $user = $this->bot->getUser();
            $qn->psid = $user->getId();
            $qn->body = $this->quest;
            $qn->save();
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

    public static function sendConversationRapidpro()
    {
        $url = "https://hiwa.tmcg.co.ug/c/ex/212eeb3d-f556-4045-a845-54eb344ec7d5/receive";
        $message = "hI THIS IS A TEST";
        $userPsid = "";
//        $serverDetails = RapidproServer::where("Status", 1)->first();
        $date = date('Y-m-d H:i:s');
        $json = "{
                    'sender': '',
                    'text': $message,
                    'date':$date + '.180Z'
                  }";


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: 	 application/json']);
        curl_exec($ch);
        curl_close($ch);




    }
}
