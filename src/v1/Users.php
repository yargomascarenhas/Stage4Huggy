<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Lib\PDOFilter as Filter;
use \Lib\Pagination as Pagination;
use Exception;

final class Users extends Pagination{
    private $defaultSort = '-id';
    private $dictonary = [
        'id' => 'id',
        'name' => 'name'
    ];

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    private function select(Filter $filter, Request $request, $id = null){
        try {
            $queryParams = $request->getQueryParams();

            $where = $filter->getWhere();
            $param = $filter->getParam();

            $id = empty($id) ? $request->getAttribute('route')->getArgument('id') : $id;
            // $user_id = (!empty($this->user->user_id)) ? $this->user->user_id : $id;

            if(empty($id)) {
                $filter->setLimit(0, 20);
                $query = "SELECT count(1) as count
                          FROM user ".$filter->getWhere();
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($param);
                $result = $stmt->fetchAll();
                $count = !empty($result[0]) ? $result[0]['count'] : 0;
                parent::setCount($count);
                $limit = parent::getLimit($request);
                $filter->setLimit($limit[0], $limit[1]);
                $ret['_links'] = parent::getLinks($request);
            }

            $query = "SELECT
                             api_id,
                             name,
                             email,
                             perfil,
                             updated_at
                        FROM user " . $filter->getWhere();
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($filter->getParam());
            $result = $stmt->fetchAll();

            $ret['code'] = HC_SUCCESS;
            $ret['data'] = [];

            if(count($result) > 0){
                foreach($result as $reg){
                    // $isme = ($user_id == $reg['id']) ? true : false;
                    $ret['data'][] =
                    [
                        'api_id' => $reg['api_id'],
                        'name' => $reg['name'],
                        'email' => $reg['email'],
                        'perfil' => $reg['perfil'],
                        'updated_at' => $reg['updated_at']
                    ];
                }
            }
        } catch (Exception $e) {
            $ret['code'] = HC_API_ERROR;
            $ret['error']['message'] = $e->getMessage();
        }

        return $ret;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param array                                    $args
     *
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function get(Request $request, Response $response, $args, $id = null){
        $queryParams = $request->getQueryParams();
        $ret = [];

        $filter = new Filter();

        $limit = parent::getLimit($request);
        $filter->setLimit($limit[0], $limit[1]);

        $id = empty($id) ? $request->getAttribute('route')->getArgument('id') : $id;
        if(empty($id)){
            $sort = empty($queryParams['sort']) ? $this->defaultSort : $queryParams['sort'];
            $filter->setOrder(parent::getOrder($sort, $this->dictonary));

            if(!empty($queryParams['name'])){
                $filter->addFilter('AND name = :name', array(':name' => $queryParams['name']));
            }
        } else {
            $filter->addFilter('AND id = :id', array(':id' => $id));
        }

        $ret = $this->select($filter, $request);
        return $response->withJson($ret)->withStatus($ret['code']);
    }

        /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param array                                    $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(Request $request, Response $response, $args){
        $body = $request->getParsedBody();
        $ret = [];
        $filter = new Filter();

        try {

            $param = [
                ':login' => $body['login'],
                ':password' => $body['password']
            ];

            $query = "SELECT IF(COUNT(1)>0, TRUE, FALSE) AS exist, id
                      FROM user
                      WHERE email = :email
                        AND password = :password";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($param);
            $result = $stmt->fetchAll();

            $p_status = (!empty($result[0]['exist']) && $result[0]['exist'] === '1') ? true : false;
            $id = !empty($result[0]['id']) ? $result[0]['id'] : null;

            if($p_status){
                $filter->addFilter('AND id = :id', array(':id' => $id));
                $ret = $this->select($filter, $request, $id);
                $payload = [
                                "iat"   => time(),
                                "exp"   => time() + (30 * 24 * 60 * 60), // 30 dias
                                "user_id" => $id,
                                "data" => json_encode($ret)
                            ];
                $ret['token'] = JWT::encode($payload, $_SERVER['JWT_SECURE']);
                $ret['code'] = HC_SUCCESS_CREATED;
            } else {
                $ret['code'] = HC_ERROR_ARGUMENTS;
            }

        } catch (Exception $e) {
            $ret['code'] = HC_API_ERROR;
            $ret['error']['message'] = $e->getMessage();
        }
        return $response->withJson($ret)->withStatus($ret['code']);
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