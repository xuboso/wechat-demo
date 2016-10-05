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

    // 被动回复消息
    public function reply(Request $request)
    {
        $xml = $request->getContent();
        $parse = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $message_content = [];
        foreach ($parse as $key => $value) {
            $message_content[$key] = trim($value);
        }
        
        // $response_content = $this->responseTextMessage($message_content);
        $response_content = $this->responseArticleMessage($message_content);
        return response($response_content)->header('Content-Type', 'text/xml');
    }


    // 生成文本消息
    private function responseTextMessage($data)
    {
        $response_xml = new \DOMDocument('1.0', 'UTF-8');
        $base = $response_xml->appendChild($response_xml->createElement('xml'));

        // 消息接收者
        $to_user = $base->appendChild($response_xml->createElement('ToUserName'));
        $to_user->appendChild($response_xml->createCDATASection($data['FromUserName']));

        // 消息发送者
        $from_user = $base->appendChild($response_xml->createElement('FromUserName'));
        $from_user->appendChild($response_xml->createCDATASection($data['ToUserName']));

        // 发送时间
        $send_time = $base->appendChild($response_xml->createElement('CreateTime'));
        $send_time->appendChild($response_xml->createTextNode(time()));

        //消息类型
        $message_type = $base->appendChild($response_xml->createElement('MsgType'));
        $message_type->appendChild($response_xml->createCDATASection('text'));

        // 消息内容
        $message_content = $base->appendChild($response_xml->createElement('Content'));
        $message_content->appendChild($response_xml->createCDATASection('你好,这是测试消息'));

        $response_xml->formatOutput = true; 
        return $response_xml->saveXML();
    }

    // 生成图文消息
    private function responseArticleMessage($data)
    {
        $response_xml = new \DOMDocument('1.0', 'UTF-8');
        $base = $response_xml->appendChild($response_xml->createElement('xml'));

        // 消息接收者
        $to_user = $base->appendChild($response_xml->createElement('ToUserName'));
        $to_user->appendChild($response_xml->createCDATASection($data['FromUserName']));

        // 消息发送者
        $from_user = $base->appendChild($response_xml->createElement('FromUserName'));
        $from_user->appendChild($response_xml->createCDATASection($data['ToUserName']));

        // 发送时间
        $send_time = $base->appendChild($response_xml->createElement('CreateTime'));
        $send_time->appendChild($response_xml->createTextNode(time()));

        //消息类型
        $message_type = $base->appendChild($response_xml->createElement('MsgType'));
        $message_type->appendChild($response_xml->createCDATASection('news'));

        // 图文数量
        $message_count = $base->appendChild($response_xml->createElement('ArticleCount'));
        $message_count->appendChild($response_xml->createTextNode(2));

        // 图文详情
        $articles = [
            [
                'title' => '介绍PHP Trait',
                'description' => 'PHP Trait',
                'pic_url' => 'https://res.wx.qq.com/mpres/htmledition/images/bg/bg_logo2491a6.png',
                'url' => 'mp.weixin.qq.com',
            ],
            [
                'title' => '在RedHat 7上搭建PHP开发环境',
                'description' => 'PHP环境搭建',
                'pic_url' => 'https://res.wx.qq.com/mpres/htmledition/images/bg/bg_logo2491a6.png',
                'url' => 'mp.weixin.qq.com',
            ],
        ];

        $articles_dom = $base->appendChild($response_xml->createElement('Articles'));

        foreach ($articles as $value) {
            $item = $articles_dom->appendChild($response_xml->createElement('item'));

            $title = $item->appendChild($response_xml->createElement('Title'));
            $title->appendChild($response_xml->createCDATASection($value['title']));

            $description = $item->appendChild($response_xml->createElement('Description'));
            $description->appendChild($response_xml->createCDATASection($value['description']));

            $pic_url = $item->appendChild($response_xml->createElement('PicUrl'));
            $pic_url->appendChild($response_xml->createCDATASection($value['pic_url']));

            $url = $item->appendChild($response_xml->createElement('url'));
            $url->appendChild($response_xml->createCDATASection($value['url']));
        }

        $response_xml->formatOutput = true;
        return $response_xml->saveXML();
    }
}