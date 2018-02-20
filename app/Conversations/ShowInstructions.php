<?php

namespace App\Conversations;

use App\Http\Controllers\FlowRunsController;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ShowInstructions extends Conversation
{
    public $bot;
    public function __construct(BotMan $bot){
        $this->bot = $bot;
    }
    /**
     * First question
     */
    public function askKitType(){
        FlowRunsController::saveRun($this->bot,3);
        $this->bot->typesAndWaits(2);
        $question = Question::create("Please select the type of Kit you are interested in")
            ->fallback('Unable to ask kit type question')
            ->callbackId('ask_kit_type')
            ->addButtons([
                Button::create('Oral (Mouth) Kit')->value('Oral'),
                Button::create('Blood Kit (Insti)')->value('Blood'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->say('Here is a short video to illustrate how you can use the HIV Self Test Kit.');
                if ($answer->getValue() == 'Oral') {
                    FlowRunsController::saveRun($this->bot,6);
                    $attachment = new Video('https://developers.tmcg.co.ug/videos/oral-kit-en.mp4',[
                        'custom_payload' => true,
                    ]);
                    $message = OutgoingMessage::create('Video')->withAttachment($attachment);
                    if (file_exists('videos/oral-kit-en.mp4')){
                        $this->bot->reply($message);
                        $this->bot->typesAndWaits(5);
                    }else{
                        $this->say('Oral Test Video Loading...');
                    }

                    $this->say('We Hope the video has been helpful. However, if you need more information on HIV Self testing, you can call 1190 toll free or visit www.beselfsure.org for more information on HIV self testing.');
                    $this->bot->typesAndWaits(2);
                    $this->say('remember to type menu to return to the main menu');
                } elseif($answer->getValue() == 'Blood') {
                    $attachment = new Video('https://developers.tmcg.co.ug/videos/blood-kit-en.mp4',[
                        'custom_payload' => true,
                    ]);
                    $message = OutgoingMessage::create('Video')->withAttachment($attachment);
                    FlowRunsController::saveRun($this->bot,7);
                    if (file_exists('videos/blood-kit-en.mp4')){
                        $this->bot->reply($message);
                        $this->bot->typesAndWaits(10);
                    }else{
                        $this->say('Blood Test Video Loading...');
                    }

                    $this->say('We Hope the video has been helpful. However, if you need more information on HIV Self testing, you can call 1190 toll free or visit www.beselfsure.org for more information on HIV self testing.');
                    $this->bot->typesAndWaits(2);
                    $this->say('remember to type menu to return to the main menu');
                }else{
                    $this->askKitType();
                }
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askKitType();
    }
}
