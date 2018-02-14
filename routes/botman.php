<?php

use App\Conversations\ShowFaqs;
use App\Http\Controllers\BotManController;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

$botman = resolve('botman');

$botman->hears('GET_STARTED', 'App\Http\Controllers\NewUserController@isNewUser');
$botman->hears('start', 'App\Http\Controllers\NewUserController@isNewUser');
$botman->hears('test', 'App\Http\Controllers\NewUserController@isNewUser');


//main menu payload
$botman->hears('faqs_1',function ($bot){
    //$bot->typesAndWaits(2);
    $bot->startConversation(new ShowFaqs($bot));
});
/*$botman->hears('instructions_2',);
$botman->hears('locations_3',);
$botman->hears('counselors_4',);*/


$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Call me {name}', function ($bot,$name) {
    $bot->reply('Your name is: '.$name);
});
$botman->hears('I want ([0-9]+)', function ($bot, $number) {
    $bot->reply('You will get: '.$number);
});
$botman->hears('.*(Hi|Hello|Hey).*', function ($bot) {
    $bot->typesAndWaits(2);
    $bot->reply('Nice to meet you!');
});

// Calling the sendSticker API for Telegram
$botman->hears('sticker', function($bot) {
    $bot->sendRequest('sendSticker', [
        'sticker' => '1234'
    ]);
});

$botman->hears('Start conversation', BotManController::class.'@startConversation');

$botman->receivesImages(function($bot, $images) {

    foreach ($images as $image) {

        $url = $image->getUrl(); // The direct url
        $title = $image->getTitle(); // The title, if available
        $payload = $image->getPayload(); // The original payload
    }
    // Create attachment
    $attachment = new Image($url);

    // Build message object
    $message = OutgoingMessage::create('This is my image text')
        ->withAttachment($attachment);

    // Reply message object
    $bot->reply($message);
});

$botman->fallback(function($bot) {
    $bot->reply('Sorry, I did not understand what you mean here! ... Please type menu to go to the main menu.');
});


$botman->hears('user', function ($bot) {
    // Access user
    $user = $bot->getUser();
    // Access Information
    $info = $user->getInfo();
    $bot->reply($user->getFirstName());
});
