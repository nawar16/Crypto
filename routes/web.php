<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use \GuzzleHttp\Client;
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test',function(){
    $response = Http::post('127.0.0.1:8000/1127017751:AAECAQiys3w7wLbK3nhONbLlb5r4mXiRPNQ/webhook',
    ['chat_id' => '860132140',
      'text' => '/getGlobal'
      ]);
    dd($response->status());
});
/*Route::get('/test', function () {
    $client = new GuzzleHttp\Client(['base_uri' => 'https://api.coinmarketcap.com/v1/ticker/']);
    $response = $client->request('GET', 'bitcoin');  
    $items = json_decode($response->getBody());
    foreach ($items as $item) {
         echo($item->id);
    }
    return $response->id;
});*/

Route::get('get-me','TelegramController@getMe');
Route::get('set-hook', 'TelegramController@setWebHook');

//Route::post(env('TELEGRAM_BOT_TOKEN') . '/webhook', 'TelegramController@handleRequest');

Route::get('/updated-activity', 'TelegramController@updatedActivity');
Route::post('/getUpdates', 'TelegramController@getUpdates');