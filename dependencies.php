<?php
use \Lib\PDOFilter as Filter;

/**
 * Set PDO container
*/
$container = $app->getContainer();
$container['pdo'] = function($c) {
    try {
        $timezone = (empty($c->request->getHeader('Timezone'))) ? $c['settings']['timezone'] : $c->request->getHeader('Timezone')[0];
        $db = $c['settings']['db'];
        $pdo = new PDO("mysql:host=" . $db['MYSQL_HOST'] . ";dbname=" . $db['MYSQL_BASE'] . ';charset=' . $db['MYSQL_CHARSET'], $db['MYSQL_USER'], $db['MYSQL_PASS'], [PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '" . $timezone . "'"]);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
};

$container['user'] = function ($container) {
    return new StdClass;
};

/**
 * Resolves container dependencies for endpoints classes
*/
$container['App\V1\Articles'] = function ($c) {
    return new App\V1\Articles($c->get('pdo'), $c->get('user'));
};

$container['App\V1\Categories'] = function ($c) {
    return new App\V1\Categories($c->get('pdo'), $c->get('user'));
};