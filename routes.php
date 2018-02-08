<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * API ROUTES
*/

/**
 * Zendesk Reports Extractor Endpoints
*/
$app->group('/v1/zendesk', function () {
    $this->get('/tickets', App\V1\Zendeskreports::class . ':gettickets');
    $this->get('/users', App\V1\Zendeskreports::class . ':getusers');
});