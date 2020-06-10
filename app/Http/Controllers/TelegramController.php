<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api as Api;
use \Telegram as Telegram;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct(){
        Telegram::setTimeout(30);
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }
    public function getMe(){
        $response = $this->telegram->getMe();
        return $response;
    }
}
