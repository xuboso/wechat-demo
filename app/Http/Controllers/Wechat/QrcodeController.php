<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use GuzzleHttp\Client as Client;
use App\Http\Controllers\Controller;

class QrcodeController extends Controller
{
    public function generate()
    {
        $client = new Client();
        $ticket = $this->getTicket();
        $request_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=";
        $response = $client->request('GET', $request_url, ['query' => ['ticket' => $ticket]]);
        $content_type = $response->getHeader('Content-Type');
        $qrcode_image = $response->getBody()->getContents();

        return response($qrcode_image)->header('Content-Type', $content_type);
    }

    protected function getTicket()
    {
        $access_token = env('WECHAT_ACCESS_TOKEN');
        $client = new Client();
        $request_url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        $request_data = [
            'expire_seconds' => 3600,
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => [
                'scene' => ['scene_str' => '20160930']
            ],
        ];

        $response = $client->request('POST', $request_url, ['json' => $request_data]);
        $response_arr = json_decode($response->getBody()->getContents(), true);
        $ticket = $response_arr['ticket'];

        return $ticket;
    }
}