<?php
require 'vendor/autoload.php';

function send_request($to, $msg){
  // 環境変数の読み込み
  $dotenv = new Dotenv\Dotenv(__DIR__);
  $dotenv->load();
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
    "text"        => $msg
  ];

  // toChannelとeventTypeは固定値
  // toは配列で渡す必要がある
  $post_body = [
    "to"        => [$to],
    "toChannel" => "1383378250",
    "eventType" => "138311608800106203",
    "content"   => $content
  ];

  // httpリクエストの作成
  $client = curl_init("https://trialbot-api.line.me/v1/events");
  curl_setopt($client, CURLOPT_POST, true);
  curl_setopt($client, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($client, CURLOPT_HTTPHEADER, $post_header);
  curl_setopt($client, CURLOPT_POSTFIELDS, json_encode($post_body));
  curl_exec($client);
  curl_close($client);
}

// callbackで来た情報の取得
$json_string     = file_get_contents('php://input');
$receive_content = json_decode($json_string)->result[0]->content;
$to              = $receive_content->from;
$receive_text    = $receive_content->text;

// 送信メッセージの作成
switch(true){
  case $receive_text == "mid":
    $msg = "Your MID is {$to}";
    break;
  case preg_match('/[起|お]こして/' ,$receive_text):
    # 時間の抽出
    # $extract_time[1] = 時間
    # $extract_time[2] = 分
    preg_match('/([0-9]+).+?([0-9]+)/', $receive_text, $extract_time);
    $set_time = $extract_time[1].":".$extract_time[2];
    $msg = "おはよう";
    break;
  default:
    $msg = $receive_text;
    break;
}

if (preg_match('/[起|お]こして/', $receive_text)){
    date_default_timezone_set('Asia/Tokyo');
    send_request($to, "わかった。${set_time}に起こすね。");
    # 指定の時間まで待機
    do{
      # 1分前から通知する
      $current_time = date("G:i", strtotime("+ 1 minute"));
      sleep(60);
    }while(strtotime($current_time) < strtotime($set_time));
  for ($c = 0; $c < 15; $c++){
    send_request($to, $msg);
    sleep(2);
  }
}
else{
  send_request($to, $msg);
}
