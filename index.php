<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Stage4Huggy
 * Describe a main index file from frontcontroller pattern of Slim Framework
 * @author Yargo Mascarenhas <yargomascarenhas@gmail.com>
 * @version 1.0.0
 */

require_once 'vendor/autoload.php'; // <-- Composer files
require_once 'config.php';

$app = new Slim\App($configSlim);

require __DIR__ . '/dependencies.php'; // <-- define slim containers

require __DIR__ . '/middlewhere.php'; // <-- setup the Cors

require __DIR__ . '/routes.php';

$app->run();
