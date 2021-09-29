<?php

require_once realpath(__DIR__ . '/vendor/autoload.php');

// Looing for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use \Firebase\JWT\JWT;
use GuzzleHttp\Client;



// Retrive env variable
$userName = $_ENV['TEST'];

echo $userName; //jfBiswajit

//define('BASE_PATH',realpath(__DIR__.'/../../'));
//putenv("UNIQID=DDD");
echo "asdf";
//echo $_SERVER["TEST"];

$t = $_ENV["TEST"];


echo $t;
sendMessage("test test test",$_ENV["ACCOUNTID"]);


  function sendMessage($message, $accountId)
    {
        $accessToken = getToken();
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
        $client = new Client();
        $response = $client->request("POST", $url, $options);
        $status = (string) $response->getStatusCode();
    }
    /**
     * ���b�Z�[�W���M�̃g�[�N�����擾����B
     * https://developers.worksmobile.com/jp/document/1002002?lang=ja
     *
     * @return string �A�N�Z�X�g�[�N��
     */
    function getToken()
    {
        $jwtToken = getJwt();
        $accessToken = getAccessToken($jwtToken);
        return $accessToken;
    }
    /**
     * JWT���擾����
     *
     * @return string jwt
     */
    function getJwt()
    {
        $serverId = $_ENV["SERVERID"];
        $privateKey = $_ENV["PRIVATEKEY"];
        // ���ϐ��ɓo�^����Ƃ��ɉ��s��##newline##�ɕϊ����Ă���̂Ō��ɖ߂��B
        $privateKey = preg_replace("/##newline##/", "\n", $privateKey);
        return JWT::encode([
            "iss" => $serverId,
            "iat" => time(),
            "exp" => time() + 3600
          ], $privateKey, "RS256");
    }
    /**
     * �A�N�Z�X�g�[�N�����擾����
     *
     * @param string $jwttoken
     * @return string �A�N�Z�X�g�[�N��
     */
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
		
		echo "".$jwttoken;
        $client = new Client();
        $response = $client->request("POST", $url, $options);
        $status = (string) $response->getStatusCode();
        $body = $response->getBody();
        $json = json_decode($body, true);
		print_r( $json);
        return $json["access_token"];
    }
	
	
	
	/*
	important points
	
	1. add the bot into admin panel
	2. register bot
	
	*/