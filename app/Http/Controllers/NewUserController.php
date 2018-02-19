<?php

namespace App\Http\Controllers;

//use BotMan\BotMan\BotMan;
use App\Conversations\AskAgeAndGender;
use App\FbUser;
use App\FlowRun;
use BotMan\BotMan\Facades\BotMan;
use Illuminate\Http\Request;

class NewUserController extends Controller
{
    public function isNewUser($bot){
        // Access user
        $user = $bot->getUser();
        // Access user psid (page scoped id)
        $psid = $user->getId();
        //get db user
        $db_user = FbUser::where('user_id',$psid)->first();
        if ($db_user != null){
            if (FbUser::where('user_id',$psid)->whereNotNull('age')->first() != null){
                $menu = new AskAgeAndGender($bot);
                $menu->displayMainMenu();
            }else{
                $bot->startConversation(new AskAgeAndGender($bot));
                FlowRunsController::saveRun($bot,1);
            }
        }else{
            $this->saveNewUser($user);
            $bot->reply('Hello, Welcome to the HIV self testing assistant. Here, you will find test guides, ask questions, get answers, and speak to a health specialist if need arises. Lets proceed.');
            $bot->startConversation(new AskAgeAndGender($bot));
            FlowRunsController::saveRun($bot,1);
            //return true;
        }
    }

    public function saveNewUser($user){
        $fb_user = new FbUser;
        $fb_user->first_name = $user->getFirstName();
        $fb_user->last_name = $user->getLastName();
        $fb_user->language = $user->getLocale();
        $fb_user->timezone = $user->getTimezone();
        $fb_user->gender = $user->getGender();
        $fb_user->user_id = $user->getId();
        $fb_user->user_name = $user->getUsername();
        $fb_user->save();
    }

}
