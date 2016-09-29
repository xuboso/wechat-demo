<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use GuzzleHttp\Client as Client;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index()
    {
        $access_token = env('WECHAT_ACCESS_TOKEN');
        $wechat_url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$access_token;
        $client = new Client();
        $response = $client->request('GET', $wechat_url);

        if ($response->getStatusCode() != 200) {
            return;
        }

        $response_content = $response->getBody()->getContents();
        $response_arr = json_decode($response_content, true);

        return response()->json(['data' => $response_arr]);
    }

    public function create()
    {
        $access_token = env('WECHAT_ACCESS_TOKEN');
        $wechat_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $menu_data = [
            'button' => [
                ['name' => '后端', 'sub_button' => [
                    ['type' => 'view', 'name' => 'PHP', 'url' => 'https://xuboso.com/category/php'],
                    ['type' => 'view', 'name' => 'Linux', 'url' => 'https://xuboso.com/category/server'],
                    ['type' => 'view', 'name' => 'AWS', 'url' => 'https://xuboso.com/tags/aws'],
                    ['type' => 'view', 'name' => 'Laravel', 'url' => 'https://xuboso.com/tags/laravel'],
                ]],
                ['name' => '前端', 'sub_button' => [
                    ['type' => 'view', 'name' => 'HTML', 'url' => 'https://xuboso.com/category/html'],
                    ['type' => 'view', 'name' => 'CSS', 'url' => 'https://xuboso.com/category/css'],
                    ['type' => 'view', 'name' => 'JS', 'url' => 'https://xuboso.com/category/js'],
                    ['type' => 'view', 'name' => 'iOS', 'url' => 'https://xuboso.com/category/ios'],
                    ['type' => 'view', 'name' => 'Android', 'url' => 'https://xuboso.com/category/android'],
                ]],
                ['type' => 'click', 'name' => '其他', 'key' => '20160929_other'],
            ],
        ];

        // 解决中文微信报错问题(40033, 不合法的请求字符，不能包含\uxxxx格式的字符)
        $menu_data = json_encode($menu_data, JSON_UNESCAPED_UNICODE);

        $client = new Client();
        $response = $client->request('POST', $wechat_url, ['body' => $menu_data]);

        if ($response->getStatusCode() != 200) {
            return;
        }

        $response_content = $response->getBody()->getContents();
        $response_arr = json_decode($response_content, true);

        if ($response_arr['errcode'] == 0) {
            return response()->json(['status' => 'OK', 'message' => 'create menu successfully', 'errors' => []]);
        } else {
            return response()->json(['status' => 'FAIL', 'message' => 'create menu failed', 'errors' => $response_arr]);
        }
    }
}