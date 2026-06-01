<?php

namespace App\Http\Controllers\SMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Add this import for Log
 

class SMSController extends Controller
{
    public function sendSMS($recipient, $message){
       
       $apiKey = env('MNOTIFY_API_KEY');
        
         $msg = $message;
      
        $sender_id = "Goblust";
        
        $msg = urlencode($msg);
        
        $response = Http::post('https://api.mnotify.com/api/sms/quick', [
        'key'       => $apiKey,
        'to'        => $recipient,
        'msg'       => $message,
        'sender_id' => $sender_id
    ]);

    if ($response->failed()) {
        Log::error('Mnotify Error: ' . $response->body());
    }

        return $response;
       
     }
}
