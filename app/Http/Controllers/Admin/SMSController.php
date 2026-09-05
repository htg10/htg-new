<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TextGuruService;

class SMSController extends Controller
{
    protected $textGuru;

    public function __construct(TextGuruService $textGuru)
    {
        $this->textGuru = $textGuru;
    }

    // public static function sendSms($sms_tmp_id, $msg, $contactno)
    // {

    //     $usrnm = env('TEXTGURU_USER_ID');
    //     $pass = env('TEXTGURU_PASSWORD');
    //     $src = env('TEXTGURU_SENDER_ID');

    //     // $phn=$phone;
    //     $ch = curl_init(env('TEXTGURU_URL'));
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$usrnm&password=$pass&source=$src&dmobile=$contactno&dlttempid=$sms_tmp_id&message=$msg");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     $data = curl_exec($ch);

    //     return redirect()->back()->with('message', "SMS Notification Sent!");
    // }

    // ===============OR==================//

    // Using Http
    // public static function sendSms($smsTemplateId, $message, $contactNumber)
    // {
    //     $userId = env('TEXTGURU_USER_ID');
    //     $password = env('TEXTGURU_PASSWORD');
    //     $senderId = env('TEXTGURU_SENDER_ID');
    //     $apiUrl = env('TEXTGURU_URL');

    //     $response = Http::post($apiUrl, [
    //         'user_id' => $userId,
    //         'password' => $password,
    //         'sender_id' => $senderId,
    //         'template_id' => $smsTemplateId,
    //         'message' => $message,
    //         'recipient' => $contactNumber
    //     ]);

    //     if ($response->successful()) {
    //         return redirect()->back()->with('message', 'SMS Notification Sent!');
    //     } else {
    //         return redirect()->back()->with('error', 'Error: ' . $response->body());
    //     }
    // }

    // ===============OR==================//

    // Using curl


    public static function sendSms($smsTemplateId, $message, $contactNumber)
    {
        $userId = env('TEXTGURU_USER_ID');
        $password = env('TEXTGURU_PASSWORD');
        $senderId = env('TEXTGURU_SENDER_ID');
        $apiUrl = env('TEXTGURU_URL');

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'username' => $userId,
            'password' => $password,
            'source' => $senderId,
            'dmobile' => $contactNumber,
            'dlttempid' => $smsTemplateId,
            'message' => $message
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return redirect()->back()->with('message', 'SMS Notification Sent!');
    }



}
