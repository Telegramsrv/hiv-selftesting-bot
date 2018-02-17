<?php

namespace App\Conversations;

use App\Faq;
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
        $this->bot->reply(GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements($this->makeTemplateElements())
        );
        /*$faqs = Faq::all();
        foreach ($faqs as $faq){
            $this->say(($faq->id).': '.$faq->title);
        }
        $this->ask('Please enter the number of the FAQ to view details', function(Answer $answer) {
            // Save result
            $this_faq =  $answer->getText();
            $this->showFaqDetails($this_faq);

        });*/

    }

    public function makeTemplateElements(){
        $elements = array();
        $faqs = Faq::all();
        for ($i=0;$i<count($faqs);$i++){
            $element = Element::create($faqs[$i]->title)
                ->subtitle(substr($faqs[$i]->body,0,30).'...')
                ->image('http://developers.tmcg.co.ug/images/test-kit-types.png')
                ->addButton(ElementButton::create('visit')->url('http://developers.tmcg.co.ug'))
                ->addButton(ElementButton::create('tell me more')
                    ->payload('faq__'.$faqs[$i]->id)->type('postback'));
            array_push($elements, $element);
        }
        return $elements;
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
                $this->say($faq->body);
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
