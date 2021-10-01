<?php

require_once realpath(__DIR__ . '/vendor/autoload.php');
use \Firebase\JWT\JWT;


/*
新しいBOT登録「大事な点　、担当の方」：
	
	1. BOTを登録します。
	2. BOTはADMIN画面に行って追加します。
	3. 今はTESTというBOTは追加してあるからそれは使っています

使い方は例として一番下のLineWorksBotTestというclassに書いてあります。
*/

class LineWorksConst{
	public const APIID = "APIID";
	public const BOTNO = "BOTNO";
	public const CONSUMERKEY = "CONSUMERKEY";
	
	
	// 「下の分」この２つ「SERVERID、PRIVATEKEY」は　3か月で一回変わる「担当の方登録時ラジオボタンを押して期間を選べます。」
	//　神田さんにお願いします:https://developers.worksmobile.com/jp/console/openapi/main　
	//　に行って”Server List”作ります
	//  例：
	//  Server List(ID登録タイプ)
	//  例：ID[SERVERIDです]：9fu7a90ca98fsf433f9aa520640868kcf8fc
	//　最近もらった：２８日１０月２０２１年
	public const SERVERID = "SERVERID";
	//　private_◯◯◯◯.key　というファイルから全部コピーして行末に”￥ｎ”をつけてここに貼り付けます。
	public const PRIVATEKEY = "PRIVATEKEY";
	//　ここまで変わる。
}


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
	private $CONSTANT;
	  
	public function __construct($DEBUG){
		$this->DEBUG = $DEBUG;
		$this->CONSTANT =new LineWorksConst();
	}	  
	
	


    function sendMessage($message, $accountId)
    {
			$accessToken = $this->getToken();
			if (!$accessToken) {
				return;
			}
			//$apiId = $_ENV["APIID"];
			//$botNo = $_ENV["BOTNO"];
	    		
	    		$apiId = $this->CONSTANT->APIID;
			$botNo = $this->CONSTANT->BOTNO;
	    
	    
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
        $serverId = $this->CONSTANT->SERVERID;
        $privateKey = $this->CONSTANT->PRIVATEKEY;
		//PRIVATEKEYの環境変数でサーバーからもらったprivate keyに\nを追加しないといけないです。
		return JWT::encode([
            "iss" => $serverId,
            "iat" => time(),
            "exp" => time() + 3600
          ], $privateKey, "RS256");
    }

    function getAccessToken($jwttoken)
    {
        $apiId = $this->CONSTANT->APIID;
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
			$apiId = $this->CONSTANT->APIID;
			$botNo = $this->CONSTANT->BOTNO;
			$consumerKey = $this->CONSTANT->CONSUMERKEY;
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
				echo の"\nstatus code :\n";
				echo $status;
			}		

	}
	}
class LineWorksBotTest{	
	
	function __construct(){
		//testing
		$lineWorksBot = new LineWorksBot(false);

		//account id　にメッセージ送る：BOTから人
		//$lineWorksBot->sendMessage("test test mehedee","ACCOUNTID");
		//channel id　にメッセージ送る：BOTからGROUP”
		//channel id とる方法：”POST/lineworks/”　とう言うフォルダーにおいてあります。
		$lineWorksBot->sendMessageChannel("this is channel message","98041249");
	}
}

//$lineWorksTest = new LineWorksBotTest();

	
