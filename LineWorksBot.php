<?php

require_once realpath(__DIR__ . '/vendor/autoload.php');

// Looing for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
use \Firebase\JWT\JWT;
use GuzzleHttp\Client;






class LineWorksBot{

	/*
	@ LineWorks　ボットからメッセージを送る
	@@ 呼ぶ:
		LineWorksBot(true) // 試してDEBUGする
		LineWorksBot(false) // DEBUGしない
	@@機能：
		sendMessage(message,accountId)
			message : 送りたいメッセージ
			accountId:相手の名前[例えば：mehedee@hdn,hayashi@hdn,dtakahashi@hdn]
			　		
	*/	
	private $DEBUG=false;

	  
	public function __construct($DEBUG){
		$this->DEBUG = $DEBUG;
	}	  
	
	


    function sendMessage($message, $accountId)
    {
			$accessToken = $this->getToken();
			if (!$accessToken) {
				return;
			}
			$apiId = $_ENV["APIID"];
			$botNo = $_ENV["BOTNO"];
			$consumerKey = $_ENV["CONSUMERKEY"];
			$url = "https://apis.worksmobile.com/r/${apiId}/message/v1/bot/${botNo}/message/push";
			$options = [
				'json' => [
					"accountId" => $accountId,
					"content" => [
						"type" => "text",
						"text" => $message
					]
				],
				'headers' => [
					'Content-Type' => 'application/json;charset=UTF-8',
					'consumerKey' => $consumerKey,
					'Authorization' => "Bearer ${accessToken}"
				]
			];
			
			if ($this->DEBUG == true){
				echo "\nACCOUNT ID:\n";
				print_r($accountId);
				
			}		
			$client = new Client();
			$response = $client->request("POST", $url, $options);
			$status = (string) $response->getStatusCode();
    }

    function getToken()
    {
        $jwtToken = $this->getJwt();
        $accessToken = $this->getAccessToken($jwtToken);
        return $accessToken;
    }

    function getJwt()
    {
        $serverId = $_ENV["SERVERID"];
        $privateKey = $_ENV["PRIVATEKEY"];
		//PRIVATEKEYの環境変数でサーバーからもらったprivate keyに\nを追加しないといけないです。
		return JWT::encode([
            "iss" => $serverId,
            "iat" => time(),
            "exp" => time() + 3600
          ], $privateKey, "RS256");
    }

    function getAccessToken($jwttoken)
    {
        $apiId = $_ENV["APIID"];
        $url = "https://auth.worksmobile.com/b/${apiId}/server/token";
        $options = [
            'form_params' => [
                "grant_type" => urlencode("urn:ietf:params:oauth:grant-type:jwt-bearer"),
                "assertion" => $jwttoken					],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
            ]
        ];
		
        $client = new Client();
        $response = $client->request("POST", $url, $options);
        $status = (string) $response->getStatusCode();
        $body = $response->getBody();
        $json = json_decode($body, true);
	    
		
		
		if ($this->DEBUG == true){
			echo "\nACCESS TOKEN:\n";
			print_r($json);
			
		}		
	
        return $json["access_token"];
    }
}
	
	//$lineWorksBot = new LineWorksBot(true);
     //$lineWorksBot->sendMessage("test test mehedee",$_ENV["ACCOUNTID"]);
	
	/*
	important points
	
	1. add the bot into admin panel
	2. register bot
	
	https://forum.worksmobile.com/jp/posts/100717/roomId%E3%81%AB%E3%83%A1%E3%83%83%E3%82%BB%E3%83%BC%E3%82%B8%E9%80%81%E3%82%8B
	
	*/