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

                    $query = "SELECT IF(COUNT(1)>0, TRUE, FALSE) AS exist FROM ticket WHERE api_id = :api_id";
                    $stmt = $this->pdo->prepare($query);
                    $stmt->execute([':api_id' => $ticket->id]);
                    $result = $stmt->fetchAll();

                    $params = [
                        ':api_id' => $ticket->id,
                        ':assignee_id' => (int) $ticket->assignee_id,
                        ':subject' => $ticket->subject,
                        ':description' => $ticket->description,
                        ':priority' => $ticket->priority,
                        ':status' => $ticket->status,
                        ':type' => $ticket->type,
                        ':created_at' => $ticket->created_at,
                        ':updated_at' => $ticket->updated_at
                    ];

                    // If not exists insert a new ticket
                    if($result[0]['exist'] === '0') {
                        $this->ticketAdd($params);
                    } else {
                        // Update ticket if exists
                        $this->ticketUpd($params);
                    }
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
     * Insert ticket register in database
     *
     * @param array $params
    */
    private function ticketAdd(Array $params) {
        $query = "INSERT INTO ticket (
                    api_id,
                    assignee_id,
                    subject,
                    description,
                    priority,
                    status,
                    type,
                    created_at,
                    updated_at
                ) VALUES (
                    :api_id,
                    :assignee_id,
                    :subject,
                    :description,
                    :priority,
                    :status,
                    :type,
                    :created_at,
                    :updated_at
                )";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }

    /**
     * Update ticket register in database
     *
     * @param array $params
    */
    private function ticketUpd(Array $params) {
        $query = "UPDATE ticket
                SET assignee_id = :assignee_id,
                    subject = :subject,
                    description = :description,
                    priority = :priority,
                    status = :status,
                    type = :type,
                    created_at = :created_at,
                    updated_at = :updated_at
                WHERE api_id = :api_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
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

                    $user_id = $user->id;

                    $query = "SELECT IF(COUNT(1)>0, TRUE, FALSE) AS exist FROM user WHERE api_id = :api_id";
                    $stmt = $this->pdo->prepare($query);
                    $stmt->execute([':api_id' => $user_id]);
                    $result = $stmt->fetchAll();

                    $params = [
                        ':api_id' => (int) $user_id,
                        ':name' => $user->name
                    ];

                    // If not exists insert a new user
                    if($result[0]['exist'] === '0') {
                        $this->userAdd($params);
                    }
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
     * Insert user register in database
     *
     * @param array $params
    */
    private function userAdd(Array $params) {
        $query = "INSERT INTO user (
                    api_id,
                    name
                ) VALUES (
                    :api_id,
                    :name
                )";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }
}