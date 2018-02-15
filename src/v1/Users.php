<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Exception;

final class Users{

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    /**
     * Syncronize user register in database with Zendesk Platform
     *
     * @param object $user
    */
    public function syncUser($user) {
        $user_id = $user->id;

        $query = "SELECT IF(COUNT(1)>0, TRUE, FALSE) AS exist FROM user WHERE api_id = :api_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':api_id' => $user_id]);
        $result = $stmt->fetchAll();

        $params = [
            ':api_id' => (int) $user_id,
            ':name' => $user->name,
            ':email' => $user->email,
            ':password' => sha1('123456'),
            ':perfil' => $user->role
        ];

        // If not exists insert a new user
        if($result[0]['exist'] === '0') {
            $this->userAdd($params);
        }
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
                        ':name' => $user->name,
                        ':email' => $user->email,
                        ':password' => sha1('123456'),
                        ':perfil' => $user->role
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
                    name,
                    email,
                    password,
                    perfil
                ) VALUES (
                    :api_id,
                    :name,
                    :email,
                    :password,
                    :perfil
                )";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }
}