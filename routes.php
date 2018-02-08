<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * API ROUTES
*/

/**
 * Zendesk Reports Extractor Endpoints
*/
$app->group('/v1/zendeskreports', function () {
    $this->get('', App\V1\Zendeskreports::class . ':get');
    $this->get('/cache', App\V1\Zendeskreports::class . ':getcache');
});