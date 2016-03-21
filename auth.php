<?php
require('util.php');


//--------------------------------------
// リクエストトークンの取得
//--------------------------------------
$params = array(
    'oauth_callback' => CALLBACK_URL,
);

// 署名作成
$params = build_oauth_params('GET', RTOKEN_URL, $params, CONSUMER_SECRET);

// GET送信
$res = file_get_contents(RTOKEN_URL .'?' .http_build_query($params));

// レスポンス取得
parse_str($res, $token);
if(!isset($token['oauth_token'])){
    echo 'エラー発生';
    exit;
}
$request_token = $token['oauth_token'];


//--------------------------------------
// 認証ページにリダイレクト
//--------------------------------------
$params = array(
    'oauth_token' => $request_token,
);

// リダイレクト
header('Location: ' .AUTH_URL .'?' .http_build_query($params));

