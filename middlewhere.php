<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Allow access from any origin
*/
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
}

/**
 * Cors configuration
*/
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
    }
   if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
       header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
   }
}

/**
 * Add the PIM and other headers for response
*/
$app->add(function(Request $request, Response $response, $next) {
    $responseNew = $response->withHeader('Content-Type', 'application/json')->withHeader('X-Powered-By', $_SERVER['APPLICATION']);
    $responseNew = $next($request, $responseNew);

    return $responseNew;
});

/**
 * JWT authentication
 * Using for controls Routes
*/
$app->add(new \Slim\Middleware\JwtAuthentication([
    "header" => "Authorization",
    "path" => ["/v1"],
    "passthrough" => ["/v1/zendesk", "/v1/users", "/v1/tickets", "/v1/organizations"],
    "secret" => $_SERVER['JWT_SECURE'],
    "secure" => false,
    "callback" => function ($request, $response, $arguments) use ($container) {
        $container["user"] = $arguments["decoded"];
    },
    "error" => function ($request, $response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));