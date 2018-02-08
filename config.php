<?php

$_SERVER['REMOTE_ADDR'] = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];
$_SERVER['SERVER_NAME'] = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];

$_SERVER['MYSQL_CHARSET'] = 'utf8';
$_SERVER['MYSQL_TIMEZONE'] = '-03:00'; // i put the brazilian timezone

$_SERVER['JWT_SECURE'] = 'S4p3rStag34H4ggy';
$_SERVER['APPLICATION'] = 'STAGE4HUGGY';

$_SERVER['ZENDESK_SUBDOMAIN'] = 'cabelopoo'; // < --- change to your Zendesk Subdomain
$_SERVER['ZENDESK_USERNAME'] = 'yargomascarenhas@gmail.com'; // < ---- Change to your Zendesk E-mail
$_SERVER['ZENDESK_TOKEN'] = 'VI6zeMe6CCGu9hbYhMfZ57MG68wb0fW0oZtNoii6'; // < ---- Change to your Zendesk Token

// local server configuration
if(strpos($_SERVER['SERVER_NAME'], 'localhost') !== false || strpos($_SERVER['SERVER_NAME'], '192.168') !== false){

    $_SERVER['MYSQL_HOST'] = 'localhost';
    $_SERVER['MYSQL_PORT'] = '3306';
    $_SERVER['MYSQL_USER'] = 'root';
    $_SERVER['MYSQL_PASS'] = '';
    $_SERVER['MYSQL_BASE'] = 'stage4huggy';

    define('SERVER', 'L');
    define('PHP_TIMEZONE',  'America/Bahia');

    $configSlim = [
        'settings' => [
            'displayErrorDetails' => true,
            'addContentLengthHeader' => false,
            'db' => [
                'MYSQL_HOST' => $_SERVER['MYSQL_HOST'],
                'MYSQL_PORT' => $_SERVER['MYSQL_PORT'],
                'MYSQL_USER' => $_SERVER['MYSQL_USER'],
                'MYSQL_PASS' => $_SERVER['MYSQL_PASS'],
                'MYSQL_BASE' => $_SERVER['MYSQL_BASE'],
                'MYSQL_CHARSET' => $_SERVER['MYSQL_CHARSET'],
                'MYSQL_TIMEZONE' => $_SERVER['MYSQL_TIMEZONE']
            ],
            'pagination' => [
                'default_per_page' => 20,
                'max_per_page' => 100,
            ],
            'timezone' => '-03:00'
        ],
    ];

} else {
    // production server configuration
    define('SERVER', 'P');
    define('PHP_TIMEZONE',  'America/Bahia');

    $configSlim = [
        'settings' => [
            'displayErrorDetails' => false,
            'addContentLengthHeader' => false,
            'db' => [
                'MYSQL_HOST' => $_SERVER['MYSQL_HOST'],
                'MYSQL_PORT' => $_SERVER['MYSQL_PORT'],
                'MYSQL_USER' => $_SERVER['MYSQL_USER'],
                'MYSQL_PASS' => $_SERVER['MYSQL_PASS'],
                'MYSQL_BASE' => $_SERVER['MYSQL_BASE'],
                'MYSQL_CHARSET' => $_SERVER['MYSQL_CHARSET']
            ],
            'pagination' => [
                'default_per_page' => 20,
                'max_per_page' => 100,
            ],
            'timezone' => 'America/Bahia'
        ],
    ];
}


date_default_timezone_set(PHP_TIMEZONE);
header('Content-Type: text/html; charset=utf-8');

define('APP_NAME', 'STAGE4HUGGY');

define('LIMIT', 50);
define('LIMIT_1', 250);
define('LIMIT_2', 500);


// 200 - ok
// 201 - something has created
// 202 - assync proccess

// 400 - Wrong args
// 401 - Not authorized
// 403 - Not authorized info
// 404 - the resource do not exists
// 410 - deleted info

// 500 - server error
// 503 - server down

//HC means HTTP CODE
define('HC_SUCCESS', 200);
define('HC_SUCCESS_CREATED', 201);
define('HC_SUCCESS_ASYNC', 202);
define('HC_SUCCESS_NOCONTENT', 204);

define('HC_ERROR_ARGUMENTS', 400);
define('HC_ERROR_NOTALLOW', 401);
define('HC_ERROR_NOTALLOWINFO', 403);
define('HC_ERROR_NOTEXISTS', 404);
define('HC_ERROR_REMOVED', 410);

define('HC_API_ERROR', 500);
define('HC_API_OFF', 503);
