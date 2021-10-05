<?php

include_once "./jwt.php";


/*
新しいBOT登録「大事な点　、担当の方」：
	
	1. BOTを登録します。
	2. BOTはADMIN画面に行って追加します。
	3. 今はTESTというBOTは追加してあるからそれは使っています
	
使い方は例として一番下のLineWorksBotTestというclassに書いてあります。
*/

class LineWorksConst{
	const APIID = ""; //API ID
	
	
	const BOTNO = ""; //Bot No　例：1416112
	
	//　神田さんにお願いします:https://developers.worksmobile.com/jp/console/openapi/main　
	//　に行って”Server List”作ります
	//  例：
	//  Server List(ID登録タイプ)
	//  例：ID[SERVERIDです]：9fu7a90ca98fsf433f9aa520640868kcf8fc
	
	const SERVERID = "";
	
	//　private_◯◯◯◯.key　というファイルから全部コピーして行末に”￥ｎ”をつけてここに貼り付けます。
	const PRIVATEKEY = "";
	
	// 「下の分」「CONSUMERKEY」は　[Server API Consumer Key ] 3か月で一回変わる
	//　最近もらった：２８日１０月２０２１年
	const CONSUMERKEY = "";
	
	
	//　ここまで変わる。
	//channel id とる方法：\\10.0.32.52\社内文書etc\L1_ＴＥＭＰ\01.ユーザ用_POST\メヘディさんPOST\lineworks\lineworks.docxとう言うフォルダーにおいてあります。
	// const CHANNELID = "";
	const CHANNELID = "";
	const ACCOUNTID="";
	
	
	const MESSAGE_SEND_URL = "https://apis.worksmobile.com/r/".LineWorksConst::APIID."/message/v1/bot/".LineWorksConst::BOTNO."/message/push";
	
	function __construct($debug){
		if ($debug == true){
			echo "LineWorksConst.class";
		}		
	}
}


class LineWorksBot{

	private $channelId = "";
	private $accountId = "";


	/*
	@ LineWorks　ボットからメッセージを送る
	@@ 呼ぶ:
		LineWorksBot(true) // 試す：DEBUGする
		LineWorksBot(false) // DEBUGしない
	@@機能：
		sendMessage(message,accountId)
			message : メッセージ内容
			accountId:相手の名前[例えば：d-takahashi@hdn,hayashi@hdn,mehedee@hdn]
			　		
	*/	
	private $DEBUG=false;

	  
	public function __construct($DEBUG){
		$this->DEBUG = $DEBUG;
		
	}	  
	
	public function setAccoutId($accountId){
		$this->accountId = $accountId;

	}


	public function getAccountId(){
		return $this->accountId;		
	}

	public function setChannelId($channelId){
		$this->channelId = $channelId;

	}

	public function getChannelId(){
		return $this->channelId;
	}

	function sendMessageOnly($message)
    {
			if ($this->accountId == "" ){
				return "accountId設定が必要です";

			}
			$this->sendMessage($message,$this->accountId);


    }

    function sendMessage($message, $accountId)
    {
			$this->accountId = $accountId;
			$accessToken = $this->getToken();
			if (!$accessToken) {
				return;
			}
			//$apiId = $_ENV["APIID"];
			//$botNo = $_ENV["BOTNO"];
	    		
	    		$apiId = LineWorksConst::APIID;
			$botNo = LineWorksConst::BOTNO;
	    
	    
			$consumerKey = LineWorksConst::CONSUMERKEY;
			$url = "https://apis.worksmobile.com/r/$apiId/message/v1/bot/$botNo/message/push";

			$data = array(
				"accountId" => $this->accountId,
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
			$context  = stream_context_create($options1);
			$result = file_get_contents($url, false, $context);
			
			



			if ($this->DEBUG == true){
				echo "\nACCOUNT ID:\n";
				print_r($accountId);
				
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
        $serverId = LineWorksConst::SERVERID;
        $privateKey = LineWorksConst::PRIVATEKEY;

		$jwt = new JWT();

		return JWT::encode(array(
            "iss" => $serverId,
            "iat" => time(),
            "exp" => time() + 3600
		), $privateKey, "RS256");
    }

    function getAccessToken($jwttoken)
    {
        $apiId = LineWorksConst::APIID;
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
		
	
        
        $json = json_decode($result, true);
	    
		
		
		if ($this->DEBUG == true){
			echo "\nACCESS TOKEN:\n";
			print_r($json);
			
		}		
	
        return $json["access_token"];
    }
	
	function sendMessageChannelOnly($message)
    {
			if($this->channelId == "")
			{
				return "channelId設定が必要です";		
			}

			$this->sendMessageChannel($message, $this->channelId);

	}
	
	function sendMessageChannel($message, $channelId)
    {
			$this->channelId = $channelId;
			$accessToken = $this->getToken();
			if (!$accessToken) {
				return;
			}
			$apiId = LineWorksConst::APIID;
			$botNo = LineWorksConst::BOTNO;
			$consumerKey = LineWorksConst::CONSUMERKEY;
			$url = LineWorksConst::MESSAGE_SEND_URL;
		
			
			
			
			$data = array(
				"roomId" => $this->channelId,
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
			
			$context  = stream_context_create($options1);
			$result = file_get_contents($url, false, $context);

			if ($this->DEBUG == true){
				echo "\nACCOUNT ID:\n";
				print_r($this->channelId);

			}		

	}
}

class LineWorksBotTest{	
	
	function __construct(){
		//testing
		$lineWorksBot = new LineWorksBot(false);

		//account id　にメッセージ送る：BOTから人
		$lineWorksBot->setAccoutId(LineWorksConst::ACCOUNTID);
		$lineWorksBot->sendMessageOnly("this is a user message to mehedee: SETTER");
		echo $lineWorksBot->getAccountId();
		$lineWorksBot->sendMessage("this is a user message to  mehedee",LineWorksConst::ACCOUNTID);

         //channel id　にメッセージ送る：BOTからGROUP

		// $lineWorksBot->setChannelId(LineWorksConst::CHANNELID);
		// $lineWorksBot->sendMessageChannelOnly("this is a channel message to  mehedee");
		// echo $lineWorksBot->getChannelId();
		// $lineWorksBot->sendMessageChannel("this is a  channel message",LineWorksConst::CHANNELID);
	}
}

//試す
$lineWorksTest = new LineWorksBotTest();



