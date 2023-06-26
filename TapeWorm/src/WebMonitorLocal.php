<?php
if (!\defined('AoiMonitor')) {
    \define('AoiMonitor', 'Injected');
    $__aoi_outputBufferCallback = function ($buffer) {
        $stringEncoder = 'urlencode';
        $tryDecode = function($data) {
            if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $data) && strlen($data) % 4 == 0){
                return base64_decode($data);
            }
            if (preg_match('/%[0-9A-Fa-f]{2}/', $data)){
                return urldecode($data);
            }
            if (preg_match('/&[a-zA-Z0-9#]+;/', $data)){
                return html_entity_decode($data);
            }
            return $data;
        };
        $detect = function ($data) use ($tryDecode) {
            $rce_blacklist = "/`|var_dump|str_rot13|serialize|base64_encode|base64_decode|strrev|eval\(|assert|file_put_contents|fwrite|curl_exec\(|dl\(|readlink|popepassthru|preg_replace|preg_filter|mb_ereg_replace|register_shutdown_function|register_tick_function|create_function|array_map|array_reduce|uasort|uksort|array_udiff|array_walk|call_user_func|array_filter|usort|stream_socket_server|pcntl_exec|passthru|exec\(|system\(|chroot\(|scandir\(|chgrp\(|chown|shell_exec|proc_open|proc_get_status|popen\(|ini_alter|ini_restore|ini_set|LD_PRELOAD|ini_alter|ini_restore|ini_set|base64 -d/i";
            $sql_blacklist = "/drop |dumpfile\b|INTO FILE|union select|outfile\b|load_file\b|multipoint\(/i";
            $upload_whitelist = "/jpg|png|gif|txt/i";
            $data = @$tryDecode($data);
            if(preg_match($sql_blacklist,$data)){
                die("sqli");
            }
            if(preg_match($rce_blacklist,$data)){
                die("rce");
            }
            if(preg_match("/phar|compress.bzip2|compress.zlib/i", $data)){
                die("unser");
            }
            preg_replace("/flag{[A-Z0-9a-z_+@#$!]+}/i","",$data);
            return $data;
        };
        $waf = function ($data) use($detect) {
            return @$detect($data);
        };
        $getHeader = function () use ($stringEncoder) {
            $headerList = array();
            foreach ($_SERVER as $name => $value) {
                if (\preg_match('/^HTTP_/', $name)) {
                    $name = \strtr(\substr($name, 5), '_', ' ');
                    $name = \ucwords(\strtolower($name));
                    $name = \strtr($name, ' ', '-');
                    $headerList[$name] = $stringEncoder($value);
                }
            }
            return $headerList;
        };
        $processArray = function (&$value) use ($stringEncoder) {
            $value = $stringEncoder($value);
        };
        $requestURI = "";
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestURI = \explode('?', $_SERVER['REQUEST_URI'], 1);
            $requestURI = $requestURI[0];
        }
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'UNKNOWN';
        $remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'UNKNOWN';
        $_GET = isset($_GET) ? $_GET : array();
        \array_walk_recursive($_GET, $processArray);
        $_POST = isset($_POST) ? $_POST : array();
        \array_walk_recursive($_POST, $processArray);
        $_COOKIE = isset($_COOKIE) ? $_COOKIE : array();
        \array_walk_recursive($_COOKIE, $processArray);
        $_FILE = isset($_FILE) ? $_FILE : array();
        \array_walk_recursive($_FILE, $processArray);
        $data = array(
            'type' => 'web',
            'data' => array(
                'script' => __FILE__,
                'method' => $method,
                'uri' => $requestURI,
                'remote' => $remote,
                'header' => $getHeader(),
                'get' => $_GET,
                'post' => $_POST,
                'cookie' => $_COOKIE,
                'file' => $_FILE,
                'buffer' => $stringEncoder($buffer),
            )
        );
        var_dump($data);
        $data['data'] = @waf($data['data']);
        if ($data === false) {
            \sleep(2);
            return $buffer;
        }
        return $data;
    };
    \ob_start(@$__aoi_outputBufferCallback);
}
