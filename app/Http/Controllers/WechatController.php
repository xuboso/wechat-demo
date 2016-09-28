<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Client;

class WechatController extends Controller
{
    // Wechat validate
    public function checkValidate(Request $request)
    {
        $signature = $request->input('signature');
        $timestamp = $request->input('timestamp');
        $nonce = $request->input('nonce');
        $echostr = $request->input('echostr');

        $token = env('WECHAT_TOKEN');
        $dictionary_sort = [$token, $timestamp, $nonce];
        sort($dictionary_sort, SORT_STRING);
        $dictionary_string = implode($dictionary_sort);
        $dictionary_string = sha1($dictionary_string);


        if ($dictionary_string == $signature) {
            return response($echostr);
        } else {
            return response("The request is not from wechat server");
        }
    }
}