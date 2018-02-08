<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Zendesk\API\HttpClient AS ZendeskAPI;
use Exception;

final class Zendeskreports{

    public function __construct($pdo, $user){
        $this->pdo = $pdo;
        $this->user = $user;
    }

    /**
     * Get Tickets and Save in Database
     * Filter the fields passed for params
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param array                                    $args
     *
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function get(Request $request, Response $response, $args){
        $queryParams = $request->getQueryParams();
        $ret = [];

        try {
            $client = new ZendeskAPI($_SERVER['ZENDESK_SUBDOMAIN']);
            $client->setAuth('basic', ['username' => $_SERVER['ZENDESK_USERNAME'], 'token' => $_SERVER['ZENDESK_TOKEN']]);
            $tickets = $client->tickets()->findAll();
            $ret['data'] = $tickets;
            $ret['code'] = HC_SUCCESS;
        } catch(Exception $e) {
            $ret['code'] = HC_API_ERROR;
            $ret['message'] = $e->getMessage();
        }

        return $response->withJson($ret)->withStatus($ret['code']);
    }
}