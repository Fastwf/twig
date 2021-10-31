<?php

require_once __DIR__ . '/../vendor/autoload.php';

$_SERVER['DOCUMENT_ROOT'] = __DIR__;


if (!function_exists("apache_request_headers")) {
    function apache_request_headers()
    {
        return [];
    }
}
