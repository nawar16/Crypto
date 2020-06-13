<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api as Api;
use \Telegram as Telegram;

class TelegramController extends Controller
{
    protected $telegram;

    protected $chat_id;
    public function __construct(){
        //Telegram::setTimeout(3000);
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }
    public function getMe(){
        $response = $this->telegram->getMe();
        return $response;
    }
    /**
     * creating a URL where Telegram will send a request once a new command is entered in our bot
     * https://api.telegram.org/bot.env('TELEGRAM_BOT_TOKEN')/getWebhookInfo
     */
    public function setWebHook(){
        $url = 'https://cryptoeco.herokuapp.com/'.env('TELEGRAM_BOT_TOKEN').'/webhook';
        $response = $this->telegram->setWebHook(['url' => $url]);
        return $response == true ?  redirect()->back() : dd($response);
    }
    /**
     * except from csrf verification
     * request sent by telegram webhook
     */
    public function handleRequest(Request $request){
        $this->chat_id = $request['message']['chat']['id'];
        $this->username = $request['message']['from']['username'];
        $this->text = $request['message']['text'];
 
        //calling the appropriate method based on the user command
        switch ($this->text) {
            case '/start':
            //find all of the available commands
            case '/menu':
                $this->showMenu();
                break;
            //get crypto market global data
            case '/getGlobal':
                $this->showGlobal();
                break;
            //get prices of top 10 cryptocurrencies
            case '/getTicker':
                $this->getTicker();
                break;
            
            //get data for a specific cryptocurrency
            case '/getCurrencyTicker':
                $this->getCurrencyTicker();
                break;
            default:
                $this->checkDatabase();
        }
    }
    public function showMenu($info = null)
    {
        $message = array();
        if ($info) {
            $message .= $info . chr(10);
        }
        $message .= '/menu' . chr(10);
        $message .= '/getGlobal' . chr(10);
        $message .= '/getTicker' . chr(10);
        $message .= '/getCurrencyTicker' . chr(10);
 
        $content = array('chat_id' => $chat_id, 'text' => 'Hello');
        $this->sendMessage($content);
    }
    //getting data from CoinMarketCap and sending it to the user after formatting
    public function showGlobal()
    {
        $data = CoinMarketCap::getGlobalData();
        // returns cryptocurrency global data.
        $this->sendMessage($this->formatArray($data), true);
    }
    public function getTicker()
    {
        $data = CoinMarketCap::getTicker();
        $formatted_data = "";
 
        foreach ($data as $datum) {
            $formatted_data .= $this->formatArray($datum);
            $formatted_data .= "-----------\n";
        }
 
        $this->sendMessage($formatted_data, true);
    }
 
    //////////////////////////Handling Input//////////////////////////
    public function getCurrencyTicker()
    {
        $message = "Please enter the name of the Cryptocurrency";
        //saving a record to the databse
        Telegram::create([
            'username' => $this->username,
            'command' => __FUNCTION__//saving command, to tracking what method to do when we have multiple input handling
        ]);
 
        $this->sendMessage($message);
    }
 
    public function checkDatabase()
    {
        try {
            $telegram = Telegram::where('username', $this->username)->latest()->firstOrFail();
 
            //it will definitely be getCurrencyTicker
            if ($telegram->command == 'getCurrencyTicker') {
                $response = CoinMarketCap::getCurrencyTicker($this->text);
 
                if (isset($response['error'])) {
                    $message = 'Sorry no such cryptocurrency found';
                } else {
                    $message = $this->formatArray($response[0]);
                }
 
                Telegram::where('username', $this->username)->delete();
 
                $this->sendMessage($message, true);
            }
        } catch (Exception $exception) {
            $error = "Sorry, no such cryptocurrency found.\n";
            $error .= "Please select one of the following options";
            $this->showMenu($error);
        }
    }
 
    ////////////////////////////////////////////////////

    protected function formatArray($data)
    {
        $formatted_data = "";
        foreach ($data as $item => $value) {
            $item = str_replace("_", " ", $item);
            if ($item == 'last updated') {
                $value = Carbon::createFromTimestampUTC($value)->diffForHumans();
            }
            $formatted_data .= "<b>{$item}</b>\n";
            $formatted_data .= "\t{$value}\n";
        }
        return $formatted_data;
    }
 
    protected function sendMessage($message, $parse_html = false)
    {
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message,
        ];
 
        if ($parse_html) $data['parse_mode'] = 'HTML';
 
        $this->telegram->sendMessage($data);
    }
    public function updatedActivity()
    {
        $activity = Telegram::getUpdates();
        dd($activity);
    }
    public function getUpdates(){
        //if ($text == '/git') {
            //$reply = 'Check me on GitHub: https://github.com/Eleirbag89/TelegramBotPHP';
            // Build the reply array
            $content = ['chat_id' => $this->chat_id, 'text' => 'HI'];
            $this->telegram->sendMessage($content);
        //}
    }
}
    
