<?php
$rce_blacklist = "/calc|whoami|`|var_dump|str_rot13|serialize|base64_encode|base64_decode|strrev|eval\(|assert|file_put_contents|fwrite|curl_exec\(|dl\(|readlink|popepassthru|preg_replace|preg_filter|mb_ereg_replace|register_shutdown_function|register_tick_function|create_function|array_map|array_reduce|uasort|uksort|array_udiff|array_walk|call_user_func|array_filter|usort|stream_socket_server|pcntl_exec|passthru|exec\(|system\(|chroot\(|scandir\(|chgrp\(|chown|shell_exec|proc_open|proc_get_status|popen\(|ini_alter|ini_restore|ini_set|LD_PRELOAD|ini_alter|ini_restore|ini_set|base64 -d/i";
$sql_blacklist = "/drop |dumpfile\b|INTO FILE|union select|outfile\b|load_file\b|multipoint\(/i";
$general_blacklist = "/passwd/i";

$waf = function ($buffer) {

    $output_blacklist = "/www-data|ctf|icq|ciscn|uid=|gid=|root:x:0:0:root:\/root:/i";
    #$buffer = preg_replace($rce_blacklist,"",$buffer);
    #$buffer = preg_replace($sql_blacklist,"",$buffer);
    $buffer = preg_replace($output_blacklist,"",$buffer);
    $buffer = preg_replace("/flag{(.*?)}/i","",$buffer);

    return $buffer;
};
\ob_start(@$waf);
$postStr ="";
foreach ($_POST as $key => $value) {
    $value = preg_replace($rce_blacklist,"",$value);
    $value = preg_replace($sql_blacklist,"",$value);
    $value = preg_replace($general_blacklist,"",$value);
    $_POST[$key] = $value;
}
$getStr = "";
foreach ($_GET as $key => $value) {
    $value = preg_replace($rce_blacklist,"",$value);
    $value = preg_replace($sql_blacklist,"",$value);
    $value = preg_replace($general_blacklist,"",$value);
    $_GET[$key] = $value;
}