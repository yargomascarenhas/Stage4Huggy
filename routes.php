<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * API ROUTES
*/

/**
 * Zendesk Reports Sync
*/
$app->group('/v1/zendesk', function () {
    $this->get('/tickets', App\V1\Zendeskreports::class . ':gettickets');
    $this->get('/users', App\V1\Zendeskreports::class . ':getusers');
    $this->get('/organizations', App\V1\Zendeskreports::class . ':getorganizations');
});

/**
 * Users
*/
$app->group('/v1/users', function () {
    $this->get('', App\V1\Users::class . ':get');
    $this->get('/{id:[0-9]+}', App\V1\Users::class . ':get');
    $this->post('/login', App\V1\Users::class . ':login');
});

/**
 * Tickets
*/
$app->group('/v1/tickets', function () {
    $this->get('', App\V1\Tickets::class . ':get');
    $this->get('/{id:[0-9]+}', App\V1\Tickets::class . ':get');
});


/**
 * Organizations
*/
$app->group('/v1/organizations', function () {
    $this->get('', App\V1\Organizations::class . ':get');
    $this->get('/{id:[0-9]+}', App\V1\Organizations::class . ':get');
});