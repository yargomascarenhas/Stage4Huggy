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

        $query = "SELECT IF(COUNT(1)>0, TRUE, FALSE) AS exist, updated_at FROM user WHERE api_id = :api_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':api_id' => $user_id]);
        $result = $stmt->fetchAll();

        $params = [
            ':api_id' => (int) $user_id,
            ':name' => $user->name,
            ':email' => $user->email,
            ':password' => sha1('123456'),
            ':perfil' => $user->role,
            ':updated_at' => $user->updated_at
        ];

        // If not exists insert a new user
        if($result[0]['exist'] === '0') {
            Users::userAdd($params);
        } else if(date_format(date_create($result[0]['updated_at']), 'YmdHis') !=
            date_format(date_create($user->updated_at), 'YmdHis')) {
            Users::userUpdate($params);
        }
    }

    /**
     * Insert user register in database
     *
     * @param array $params
    */
    public function userAdd(Array $params) {
        $query = "INSERT INTO user (
                    api_id,
                    name,
                    email,
                    password,
                    perfil,
                    updated_at
                ) VALUES (
                    :api_id,
                    :name,
                    :email,
                    :password,
                    :perfil,
                    :updated_at
                )";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }

    /**
     * Update user register in database
     *
     * @param array $params
    */
    public function userUpdate(Array $params) {
        $query = "UPDATE user
                  SET name = :name,
                      email = :email,
                      password = :password,
                      perfil = :perfil,
                      updated_at = :updated_at
                  WHERE api_id = :api_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }
}