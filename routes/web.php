<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Conversations\TalkToCounselor;
use App\Conversations\TestFollowup;
use BotMan\Drivers\Facebook\FacebookDriver;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('location/{lat}/{lng}', 'LocationsController@show');

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');
Route::get('test-cronjob',function (){
    $unfollowed_users = DB::select('SELECT * FROM fb_users WHERE DATEDIFF(NOW(),created_at)<10');
    if (count($unfollowed_users)>0){
        $botman = resolve('botman');
        foreach ($unfollowed_users as $u_user){
            $botman->say('Thank you for using the HIV Self Testing ChartBot.', $u_user->user_id, FacebookDriver::class);
            $botman->startConversation(new TestFollowup($botman), $u_user->user_id, FacebookDriver::class);
        }
    }
});
Route::get('dateformat', function (){TalkToCounselor::sendConversationRapidpro();});