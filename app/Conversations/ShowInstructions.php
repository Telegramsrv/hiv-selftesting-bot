<?php

namespace App\Conversations;

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
        $question = Question::create("Please select the type of Kit you are interested in")
            ->fallback('Unable to ask kit type question')
            ->callbackId('ask_kit_type')
            ->addButtons([
                Button::create('Oral Kit (Oraquick) ')->value('Oral'),
                Button::create('Blood Kit (Insti)')->value('Blood'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'Oral') {
                    $attachment = new Video('https://developers.tmcg.co.ug/videos/oral-kit.mp4',[
                        'custom_payload' => true,
                    ]);
                    $message = OutgoingMessage::create('Video')->withAttachment($attachment);
                    $this->bot->reply($message);
                } elseif($answer->getValue() === 'Blood') {
                    //
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
