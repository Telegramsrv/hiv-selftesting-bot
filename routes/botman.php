<?php

use App\Conversations\ShowFaqs;
use App\Conversations\ShowInstructions;
use App\Conversations\ShowLocations;
use App\Conversations\ShowLocationsByCounty;
use App\Conversations\TalkToCounselor;
use App\Conversations\TestFollowup;
use App\FbUser;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\FlowRunsController;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\FacebookDriver;

DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookImageDriver::class);
DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookLocationDriver::class);

$botman = resolve('botman');

$botman->hears('GET_STARTED', 'App\Http\Controllers\NewUserController@isNewUser');
$botman->hears('start', 'App\Http\Controllers\NewUserController@isNewUser');
$botman->hears('test', 'App\Http\Controllers\NewUserController@isNewUser');
$botman->hears('st', 'App\Http\Controllers\NewUserController@isNewUser');

$botman->hears('menu','App\Conversations\AskAgeAndGender@displayMainMenu');
$botman->hears('help','App\Conversations\AskAgeAndGender@displayMainMenu');

$botman->hears('stop', function($bot) {
    $bot->reply('stopped');
})->stopsConversation();

//main menu payload
$botman->hears('faqs_1',function ($bot){
    //$bot->typesAndWaits(2);
    $bot->startConversation(new ShowFaqs($bot));
});
$botman->hears('instructions_2',function ($bot){
    //$bot->typesAndWaits(2);
    $bot->startConversation(new ShowInstructions($bot));
});
$botman->hears('locations_3',function ($bot){
    FlowRunsController::saveRun($bot,4);
    $bot->reply(ButtonTemplate::create('In order to give you relevant information, we shall use either your current location or any other location you provide to suggest the closest pharmacies or selling points.')
        ->addButton(ElementButton::create('Use my location')->type('postback')->payload('use_my_location'))
        ->addButton(ElementButton::create('Enter my county')->type('postback')->payload('choose_my_county'))
    );
    //$bot->typesAndWaits(2);
});

$botman->hears('counselors_4',function ($bot){
    //$bot->typesAndWaits(2);
    $bot->startConversation(new TalkToCounselor($bot));
});

//single faq payload details
$botman->hears('faq__{id}',function ($bot,$id){
    $faq_details = new ShowFaqs($bot);
    $faq_details->showFaqDetails($id);
});

//ask questions
$botman->hears('ask_question', function ($bot) {
    $bot->startConversation(new TalkToCounselor($bot));
});

//capture location sharing type
$botman->hears('use_my_location', function ($bot) {
    $bot->startConversation(new ShowLocations($bot));
});
$botman->hears('choose_my_county', function ($bot) {
    $bot->startConversation(new ShowLocationsByCounty($bot));
});



$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello! You can type menu to view the available options to choose from');
});
/*$botman->hears('Call me {name}', function ($bot,$name) {
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
});*/
//bot testing
$botman->hears('admin_test_my_followup', function ($bot) {
    $user = $bot->getUser();
    $psid = $user->getId();
    $bot->reply('Now testing Followup for '.$user->getFirstName());
    $bot->startConversation(new TestFollowup($bot));
});
$botman->hears('admin_reset_my_followup', function ($bot) {
    $user = $bot->getUser();
    $psid = $user->getId();
    $fb_user = FbUser::where('user_id', $psid)->first();
    $fb_user->followed = 0;
    $fb_user->save();
    $bot->reply('your followup status has been reset. please enter admin_test_my_followup to test the followup flow a gain');
});
$botman->hears('admin_reset_my_visit', function ($bot) {
    $user = $bot->getUser();
    $psid = $user->getId();
    $fb_user = FbUser::where('user_id', $psid)->first();
    $fb_user->delete();
    $bot->reply('Your visit has been deleted. please delete your conversation and click get started again to restart the whole process');
});
//end bot testing

$botman->fallback(function($bot) {
    $bot->reply('Sorry, I did not understand what you mean here! ... Please type menu to go to the main menu.');
});

