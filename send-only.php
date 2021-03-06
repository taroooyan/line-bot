<?php
require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// 環境変数の読み込み
$channel_id     = getenv('CHANNEL_ID');
$channel_secret = getenv('CHANNEL_SECRET');
$mid            = getenv('MID');

// line-botの情報
$post_header = array(
  "Content-Type: application/json; charser=UTF-8",
  "X-Line-ChannelID: ${channel_id}",
  "X-Line-ChannelSecret: ${channel_secret}",
  "X-Line-Trusted-User-With-ACL: ${mid}"
);

// テキストで返事をする場合
// "contentType"=>1,
// "toType"=>1,
$content = [
  "contentType" => 1,
  "toType"      => 1,
  "text"        => "test"
];

// toChannelとeventTypeは固定値
// toは配列で渡す必要がある
$post_body = [
  "to"        => [$argv[1]],
  "toChannel" => "1383378250",
  "eventType" => "138311608800106203",
  "content"   => $content
];

$client = curl_init("https://trialbot-api.line.me/v1/events");
curl_setopt($client, CURLOPT_POST, true);
curl_setopt($client, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
curl_setopt($client, CURLOPT_POSTFIELDS, json_encode($post_body));
curl_setopt($client, CURLOPT_HTTPHEADER, $post_header);
for ($c = 0; $c < 15; $c++){
  $response = curl_exec($client);
  sleep(2);
}
curl_close($client);
