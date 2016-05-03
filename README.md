## Initialize
`cp template.env .env`  
```.env
CHANNEL_ID="Channel ID of Line"
CHANNEL_SECRET="Channel Secret of Line"
MID="MID of Line"
```

## How to
|送信テキスト|処理内容|
|---|---|
|mid|送信者のmidを返す|
|"時間"におこして|指定した時間にメッセージが来る|
|それ以外|オウム返しをする|
時間は[0-23]:[0-59]のように24時間形式で指定する. また指定した時間は次回のその時間になるため今のところ日付を指定できない.

