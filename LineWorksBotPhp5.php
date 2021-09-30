<?php

require_once realpath(__DIR__ . '/vendor/autoload.php');

// Looing for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
use \Firebase\JWT\JWT;






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
			$url = "https://apis.worksmobile.com/r/$apiId/message/v1/bot/$botNo/message/push";

			$data = array(
				"accountId" => $accountId,
				"content" => array(
					"type" => "text",
					"text" => $message
				)
					
			);
			$options1 = array(
					'http' => array(
						'header'  => "Content-Type:application/json;charset=UTF-8\r\n".
						"Authorization:Bearer ".$accessToken."\r\n".
						"consumerKey:".$consumerKey."\r\n",
						'method'  => 'POST',
						'content' => json_encode($data),
				)
			);
			print_r($options1);
			$context  = stream_context_create($options1);
			$result = file_get_contents($url, false, $context);
			
			



			if ($this->DEBUG == true){
				echo "\nACCOUNT ID:\n";
				print_r($accountId);
				echo "\nstatus code :\n";
				echo $status;
			}		

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
	
		$data = array(
			   "grant_type" => urlencode("urn:ietf:params:oauth:grant-type:jwt-bearer"),
                "assertion" => $jwttoken	
		);
		$options1 = array(
				'http' => array(
					'header'  => "Content-Type:application/x-www-form-urlencoded; charset=UTF-8",
					'method'  => 'POST',
					'content' => http_build_query($data),
			)
		);

		$context  = stream_context_create($options1);
		$result = file_get_contents($url, false, $context);
		echo "test";
		print_r($result);
		echo "test";
	
        
        $json = json_decode($result, true);
	    
		
		
		if ($this->DEBUG == true){
			echo "\nACCESS TOKEN:\n";
			print_r($json);
			
		}		
	
        return $json["access_token"];
    }
	
	
	
	function sendMessageChannel($message, $channelNo)
    {
			$accessToken = $this->getToken();
			if (!$accessToken) {
				return;
			}
			$apiId = $_ENV["APIID"];
			$botNo = $_ENV["BOTNO"];
			$consumerKey = $_ENV["CONSUMERKEY"];
			$url = "https://apis.worksmobile.com/r/${apiId}/message/v1/bot/${botNo}/message/push";
		
			
			
			
			$data = array(
				"roomId" => $channelNo,
				"content" => array(
					"type" => "text",
					"text" => $message
				)
					
			);
			$options1 = array(
					'http' => array(
						'header'  => "Content-Type:application/json;charset=UTF-8\r\n".
						"Authorization:Bearer ".$accessToken."\r\n".
						"consumerKey:".$consumerKey."\r\n",
						'method'  => 'POST',
						'content' => json_encode($data),
				)
			);
			print_r($options1);
			$context  = stream_context_create($options1);
			$result = file_get_contents($url, false, $context);

			if ($this->DEBUG == true){
				echo "\nACCOUNT ID:\n";
				print_r($channelNo);
				echo "\nstatus code :\n";
				echo $status;
			}		

	}
	}
	
	//testing
	$lineWorksBot = new LineWorksBot(false);
	
	//account id
	//$lineWorksBot->sendMessage("test test mehedee",$_ENV["ACCOUNTID"]);
	//channel id
	$lineWorksBot->sendMessageChannel("this is channel message","98041249");
	
	
	
	
	/*
	important points
	
	1. add the bot into admin panel
	2. register bot
	
	https://forum.worksmobile.com/jp/posts/100717/roomId%E3%81%AB%E3%83%A1%E3%83%83%E3%82%BB%E3%83%BC%E3%82%B8%E9%80%81%E3%82%8B
	
	*/
