<?php

namespace App\Conversations;

use App\Faq;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ShowFaqs extends Conversation
{
    public $bot;
    public function __construct(BotMan $bot){
        $this->bot = $bot;
    }
    /**
     * First question
     */
    public function showAllFaqs()
    {
        $faqs = Faq::all();
        foreach ($faqs as $faq){
            $this->say(($faq->id).': '.$faq->title);
        }
        $this->ask('Please enter the number of the FAQ to view details', function(Answer $answer) {
            // Save result
            $this_faq =  $answer->getText();
            $this->showFaqDetails($this_faq);

        });

    }

    public function showFaqDetails($this_faq){
        $faq = Faq::find($this_faq);
        if ($faq != null){
            if ($faq->image != null){
                $attachment = new Image(asset('images/'.$faq->image));
                $message = OutgoingMessage::create($faq->body)
                    ->withAttachment($attachment);
                $this->say($faq->title);
                $this->bot->reply($message);
            }else{
                $this->say($faq->title);
                $this->say($faq->body);
            }
        }else{
            $this->ask('Please enter a correct number', function(Answer $answer) {
                // Save result
                $this_faq =  $answer->getText();
                $this->showFaqDetails($this_faq);

            });
        }
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->showAllFaqs();
    }
}
