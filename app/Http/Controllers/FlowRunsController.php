<?php

namespace App\Http\Controllers;

use App\FlowRun;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;

class FlowRunsController extends Controller
{
    public static function saveRun(BotMan $bot, $flow_id){
        $user = $bot->getUser();
        $flow_run = new FlowRun;
        $flow_run->psid = $user->getId();
        $flow_run->flow_id = $flow_id;
        $flow_run->save();
    }
}
