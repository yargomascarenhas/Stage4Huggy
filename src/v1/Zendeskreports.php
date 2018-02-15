<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Zendesk\API\HttpClient AS ZendeskAPI;
use Exception;

final class Zendeskreports{

    protected $zendesk;

    public function __construct($pdo){
        $this->pdo = $pdo;
        $this->zendesk = new ZendeskAPI($_SERVER['ZENDESK_SUBDOMAIN']);
        $this->zendesk->setAuth('basic', ['username' => $_SERVER['ZENDESK_USERNAME'], 'token' => $_SERVER['ZENDESK_TOKEN']]);
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
    public function gettickets(Request $request, Response $response, $args){
        $queryParams = $request->getQueryParams();
        $ret = [];

        try {
            $ticketsapi = $this->zendesk->tickets()->findAll();

            if(!empty($ticketsapi->tickets)) {
                $tickets = $ticketsapi->tickets;

                foreach($tickets as $ticket) {
                    \App\V1\Tickets::syncTicket($ticket);
                }
            } else {
                throw new Exception('No users finded');
            }

            $ret['data'] = $tickets;
            $ret['code'] = HC_SUCCESS;
        } catch(Exception $e) {
            $ret['code'] = HC_API_ERROR;
            $ret['message'] = $e->getMessage();
        }

        return $response->withJson($ret)->withStatus($ret['code']);
    }

    /**
     * Get Users and Save in Database
     * Filter the fields passed for params
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param array                                    $args
     *
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function getusers(Request $request, Response $response, $args){
        $queryParams = $request->getQueryParams();
        $ret = [];

        try {
            $usersapi = $this->zendesk->users()->findAll();

            if(!empty($usersapi->users)) {
                $users = $usersapi->users;

                foreach($users as $user) {
                    \App\V1\Users::syncUser($user);
                }
            } else {
                throw new Exception('No users finded');
            }

            $ret['data'] = $users;
            $ret['code'] = HC_SUCCESS;
        } catch(Exception $e) {
            $ret['code'] = HC_API_ERROR;
            $ret['message'] = $e->getMessage();
        }

        return $response->withJson($ret)->withStatus($ret['code']);
    }

    /**
     * Get Users and Save in Database
     * Filter the fields passed for params
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param array                                    $args
     *
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function getorganizations(Request $request, Response $response, $args){
        $queryParams = $request->getQueryParams();
        $ret = [];

        try {
            $organizationsapi = $this->zendesk->organizations()->findAll();

            if(!empty($organizationsapi->organizations)) {
                $organizations = $organizationsapi->organizations;

                foreach($organizations as $organization) {
                    \App\V1\organizations::syncOrganization($organization);
                }
            } else {
                throw new Exception('No organizations finded');
            }

            $ret['data'] = $organizations;
            $ret['code'] = HC_SUCCESS;
        } catch(Exception $e) {
            $ret['code'] = HC_API_ERROR;
            $ret['message'] = $e->getMessage();
        }

        return $response->withJson($ret)->withStatus($ret['code']);
    }
}