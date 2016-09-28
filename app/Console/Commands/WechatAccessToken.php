<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client as Client;

class WechatAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:access_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Wechat Access Token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $access_token = $this->getAccessToken();

        $env_path = base_path('.env');
        $patterns = $replacements = [];

        $patterns[0] = "/WECHAT_ACCESS_TOKEN=([\S]*)/";
        $replacements[0] = "WECHAT_ACCESS_TOKEN=".$access_token;

        if (!file_exists($env_path)) {
            $this->error('The file .env not exists!');
        }

        // update the access token
        file_put_contents($env_path, preg_replace($patterns, $replacements, file_get_contents($env_path)));
        $this->info(PHP_EOL.'The Wechat access token has refreshed!'.PHP_EOL);
    }

    // Get access token from wechat server
    protected function getAccessToken()
    {
        $app_id = env('WECHAT_APP_ID');
        $app_secret = env('WECHAT_APP_SECRET');

        $client = new Client();
        $response = $client->request('GET', 'https://api.weixin.qq.com/cgi-bin/token', [
            'query' => ['grant_type' => 'client_credential', 'appid' => $app_id, 'secret' => $app_secret]
        ]);

        if ($response->getStatusCode() != 200) {
            return;
        }

        $response_content = $response->getBody()->getContents();
        $response_arr = json_decode($response_content, true);

        $access_token = $response_arr['access_token'];

        return $access_token;
    }
}
