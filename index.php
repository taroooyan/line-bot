<?php
require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// 環境変数の読み込み
$channel_id = getenv('CHANNEL_ID');
$channel_secret = getenv('CHANNEL_SECRET');
$mid = getenv('MID');

// line-botの情報
$post_header = array(
  "Content-Type: application/json; charser=UTF-8",
  "X-Line-ChannelID: ${channel_id}",
  "X-Line-ChannelSecret: ${channel_secret}",
  "X-Line-Trusted-User-With-ACL: ${mid}"
);

// callbackで来た情報の取得
$json_string = file_get_contents('php://input');
$jsonObj = json_decode($json_string);
$to = $jsonObj->{"result"}[0]->{"content"}->{"from"};

// テキストで返事をする場合
$send_msg = [
  'contentType'=>1,
  "toType"=>1,
  "text"=>"hello\nYour Mid = {$to}" ];

// toChannelとeventTypeは固定値
// toは配列で渡す必要がある
$post_body = [
  "to"=>[$to],
  "toChannel"=>"1383378250",
  "eventType"=>"138311608800106203",
  "content"=>$send_msg ];

$client = curl_init("https://trialbot-api.line.me/v1/events");
curl_setopt($client, CURLOPT_POST, true);
curl_setopt($client, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
curl_setopt($client, CURLOPT_POSTFIELDS, json_encode($post_body));
curl_setopt($client, CURLOPT_HTTPHEADER, $post_header);

$response = curl_exec($client);
curl_close($client);
