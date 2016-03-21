<?php
require('util.php');

// restore the token and secret
session_start();
if (empty($_SESSION['oauth_token']) || empty($_SESSION['oauth_token_secret'])) {
    echo 'empty token or token_secret.';
    exit;
}
$access_token = $_SESSION['oauth_token'];
$access_token_secret = $_SESSION['oauth_token_secret'];


//--------------------------------------
// ユーザーの設定情報を取得してみる
//--------------------------------------
$params = array(
    'oauth_token' => $access_token,
);

$params = build_oauth_params('GET', STREAM_URL, $params, CONSUMER_SECRET, $access_token_secret);
ksort($params);
array_walk($params, create_function('&$v,$k', '$v = "{$k}=\"".rawurlencode($v)."\"";'));

// curl write func
class CurlWritefunc
{
    // max loop
    const MAX_COUNT = 100;
    // loop counter
    private $count = 1;
    // read buffer
    private $buff = '';
    // callback
    public function callback($ch, $input) {
        $this->buff .= $input;
        // loop while "\n" exists
        while (($pos = strpos($this->buff, "\n")) !== false) {
            // get one line
            $line = substr($this->buff, 0, $pos);
            // reduce the buffer
            $this->buff = substr($this->buff, $pos+1);
            // get json data
            $data = json_decode($line);
            // debug: file_put_contents('/tmp/stream.out', var_export($data, true)."\n", FILE_APPEND);
            // output
            echo "No. {$this->count}: ";
            if (isset($data->id) && isset($data->text) && isset($data->user->name)) {
                // tweet
                echo "[{$data->id}] {$data->text} by {$data->user->name}";
            } elseif (empty($data)) {
                // empty
                echo 'EMPTY';
            } elseif (isset($data->friends)) {
                // friends
                echo "your friends id: " . implode(', ', $data->friends);
            } else {
                // unknown
                echo 'UNKNOWN FORMAT';
            }
            echo "<br />\n";
            // loop end
            if ($this->count++ > self::MAX_COUNT) throw new Exception('max loop');
            // flush
            ob_flush();
            flush();
        }
        // return read bytes count
        return strlen($input);
    }
}
$cw = new CurlWritefunc();

// curl init
$ch = curl_init(STREAM_URL);

// curl options
if (!curl_setopt_array($ch, array(
    CURLOPT_HTTPHEADER => array('Authorization: OAuth ' . implode(', ', $params)),
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_WRITEFUNCTION => array($cw, 'callback'),
))) {
    echo 'failed to set curl options.';
    exit;
}

// curl exec
try {
    curl_exec($ch);
} catch (Exception $e) {
    echo 'Exception: ' . $e->getMessage();
    if (!curl_errno($ch)) {
        echo '(curl: [' .curl_errno($ch) .'] ' .curl_error($ch) . ')';
        var_dump(curl_getinfo($ch));
    }
}
        echo '(curl: [' .curl_errno($ch) .'] ' .curl_error($ch) . ')';

// curl close
curl_close($ch);

?></div>
<div>
END
</div>
</body>
</html>
