<?php

// アプリケーション設定
define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
if (isset($_SERVER['HTTP_HOST'])) {
    define('CALLBACK_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/callback.php');
}

// URL
define('RTOKEN_URL', 'https://api.twitter.com/oauth/request_token');
define('AUTH_URL', 'https://api.twitter.com/oauth/authenticate');
define('TOKEN_URL', 'https://api.twitter.com/oauth/access_token');
define('INFO_URL', 'https://api.twitter.com/1.1/account/settings.json');
define('STREAM_URL', 'https://userstream.twitter.com/1.1/user.json');
define('SAMPLE_URL', 'https://userstream.twitter.com/1.1/statuses/sample.json');


/*
 * 署名の作成
 */
function build_oauth_params($method, $url, $params, $consumer_secret, $token_secret = '') {
    // base params
    $params = array_merge(array(
        'oauth_consumer_key' => CONSUMER_KEY,
        'oauth_nonce' => md5(microtime() . mt_rand()),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_version' => '1.0',
    ), $params);
    ksort($params);
    $base = rawurlencode($method) .'&' .rawurlencode($url) .'&' .rawurlencode(http_build_query($params));
    $key = rawurlencode($consumer_secret) .'&' .rawurlencode($token_secret);
    $params['oauth_signature'] = base64_encode(hash_hmac('sha1', $base, $key, true));
    return $params;
}

