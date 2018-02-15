<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Lib\PDOFilter as Filter;
use \Lib\Pagination as Pagination;
use Exception;

final class Organizations extends Pagination{

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
                             updated_at
                        FROM organization " . $filter->getWhere();
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
    public function get(Request $request, Response $response, $args){
        $queryParams = $request->getQueryParams();
        $ret = [];

        $filter = new Filter();

        $limit = parent::getLimit($request);
        $filter->setLimit($limit[0], $limit[1]);

        $id = $request->getAttribute('route')->getArgument('id');
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
     * Syncronize organization register in database with Zendesk Platform
     *
     * @param object $organization
    */
    public function syncOrganization($organization) {
        $organization_id = $organization->id;

        $query = "SELECT IF(COUNT(1)>0, TRUE, FALSE) AS exist, updated_at FROM organization WHERE api_id = :api_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':api_id' => $organization_id]);
        $result = $stmt->fetchAll();

        $params = [
            ':api_id' => $organization_id,
            ':name' => $organization->name,
            ':updated_at' => $organization->updated_at
        ];

        // If not exists insert a new organization
        if($result[0]['exist'] === '0') {
            Organizations::organizationAdd($params);
        } else if(date_format(date_create($result[0]['updated_at']), 'YmdHis') !=
            date_format(date_create($organization->updated_at), 'YmdHis')) {
            Organizations::organizationUpdate($params);
        }
    }

    /**
     * Insert organization register in database
     *
     * @param array $params
    */
    public function organizationAdd(Array $params) {
        $query = "INSERT INTO organization (
                    api_id,
                    name,
                    updated_at
                ) VALUES (
                    :api_id,
                    :name,
                    :updated_at
                )";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }

    /**
     * Update organization register in database
     *
     * @param array $params
    */
    public function organizationUpdate(Array $params) {
        $query = "UPDATE organization
                  SET name = :name,
                      updated_at = :updated_at
                  WHERE api_id = :api_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }
}