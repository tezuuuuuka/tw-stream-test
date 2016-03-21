<?php
require('util.php');


//--------------------------------------
// アクセストークンの取得
//--------------------------------------
$params = array(
    'oauth_verifier' => $_GET['oauth_verifier'],
    'oauth_token' => $_GET['oauth_token'],
);

// 署名作成
$params = build_oauth_params('POST', TOKEN_URL, $params, CONSUMER_SECRET);

// POST送信
$options = array('http' => array(
    'method' => 'POST',
    'header' => 'Content-type: application/x-www-form-urlencoded',
    'content' => http_build_query($params),
));
$res = file_get_contents(TOKEN_URL, false, stream_context_create($options));

// レスポンス取得
parse_str($res, $token);
if (empty($token['oauth_token']) || empty($token['oauth_token_secret'])) {
    echo 'empty token or token_secret.';
    exit;
}
session_start();
$_SESSION['oauth_token'] = $token['oauth_token'];
$_SESSION['oauth_token_secret'] = $token['oauth_token_secret'];

// redirect
header('Location: stream.php');

