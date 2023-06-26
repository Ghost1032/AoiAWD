<?php
$postData = function ($url, $data) {
    $server = \stream_socket_client($url);
    if ($server) {
        \fwrite($server, $data . "\n");
        return \base64_decode(\rtrim(\fgets($server)));
    }
    return false;
};
$__ghost_url = "127.0.0.1:1033";
@$postData($__ghost_url,"Ping");