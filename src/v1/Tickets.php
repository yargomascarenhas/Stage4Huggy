<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Lib\PDOFilter as Filter;
use \Lib\Pagination as Pagination;
use Exception;

final class Tickets extends Pagination{
    private $defaultSort = '-api_id';
    private $dictonary = [
        'api_id' => 'api_id',
        'subject' => 'subject',
        'description' => 'description',
        'status' => 'status',
        'type' => 'type',
        'updated_at' => 'updated_at'
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
                          FROM ticket ".$filter->getWhere();
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
                             assignee_id,
                             assignee_name,
                             subject,
                             description,
                             priority,
                             status,
                             type,
                             created_at,
                             updated_at,
                             requester_id,
                             requester_name,
                             organization_id,
                             organization_name,
                             satisfaction_rating,
                             tags
                        FROM vw_ticket " . $filter->getWhere();
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
                        'assignee_id' => $reg['assignee_id'],
                        'assignee_name' => $reg['assignee_name'],
                        'subject' => $reg['subject'],
                        'description' => $reg['description'],
                        'priority' => $reg['priority'],
                        'status' => $reg['status'],
                        'type' => $reg['type'],
                        'created_at' => $reg['created_at'],
                        'updated_at' => $reg['updated_at'],
                        'requester_id' => $reg['requester_id'],
                        'requester_name' => $reg['requester_name'],
                        'organization_id' => $reg['organization_id'],
                        'organization_name' => $reg['organization_name'],
                        'satisfaction_rating' => $reg['satisfaction_rating'],
                        'tags'=> $reg['tags']
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
        // api_id,
        // assignee_id,
        // assignee_name,
        // subject,
        // description,
        // priority,
        // status,
        // type,
        // created_at,
        // updated_at,
        // requester_id,
        // requester_name,
        // organization_id,
        // organization_name,
        // satisfaction_rating,
        // tags
        $id = $request->getAttribute('route')->getArgument('id');
        if(empty($id)){
            $sort = empty($queryParams['sort']) ? $this->defaultSort : $queryParams['sort'];
            $filter->setOrder(parent::getOrder($sort, $this->dictonary));

            if(!empty($queryParams['id'])){
                $filter->addFilter('AND api_id = :id', array(':id' => $queryParams['id']));
            }
            if(!empty($queryParams['tags'])){
                $filter->addFilter('AND FIND_IN_SET(tags, :tags) > 0', array(':tags' => $queryParams['tags']));
            }
            if(!empty($queryParams['requester_id'])){
                $filter->addFilter('AND requester_id = :requester_id', array(':requester_id' => $queryParams['requester_id']));
            }
            if(!empty($queryParams['organization_id'])){
                $filter->addFilter('AND organization_id = :organization_id', array(':organization_id' => $queryParams['organization_id']));
            }
            if(!empty($queryParams['priority'])){
                $filter->addFilter('AND priority = :priority', array(':priority' => $queryParams['priority']));
            }
            if(!empty($queryParams['status'])){
                $filter->addFilter('AND status = :status', array(':status' => $queryParams['status']));
            }
            if(!empty($queryParams['type'])){
                $filter->addFilter('AND type = :type', array(':type' => $queryParams['type']));
            }
        } else {
            $filter->addFilter('AND api_id = :id', array(':id' => $id));
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
    public function groupStatus(Request $request, Response $response, $args){
        $ret = [];

        $filter = new Filter();

        try {
            $queryParams = $request->getQueryParams();

            $where = $filter->getWhere();
            $param = $filter->getParam();

            $id = empty($id) ? $request->getAttribute('route')->getArgument('id') : $id;

            $query = "SELECT
                        COUNT(1) AS total,
                        status
                    FROM vw_ticket " . $filter->getWhere()
                    . " GROUP BY status";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($filter->getParam());
            $result = $stmt->fetchAll();

            $ret['code'] = HC_SUCCESS;
            $ret['data'] = [];

            if(count($result) > 0){
                foreach($result as $reg){
                    $ret['data'][] =
                    [
                        'status' => $reg['status'],
                        'total' => $reg['total']
                    ];
                }
            }
        } catch (Exception $e) {
            $ret['code'] = HC_API_ERROR;
            $ret['error']['message'] = $e->getMessage();
        }

        return $response->withJson($ret)->withStatus($ret['code']);
    }


    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param array                                    $args
     *
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function groupSatisfaction(Request $request, Response $response, $args){
        $ret = [];

        $filter = new Filter();
        $filter->addFilter('AND satisfaction_rating IS NOT NULL');

        try {
            $queryParams = $request->getQueryParams();

            $where = $filter->getWhere();
            $param = $filter->getParam();

            $id = empty($id) ? $request->getAttribute('route')->getArgument('id') : $id;

            $query = "SELECT
                        COUNT(1) AS total,
                        satisfaction_rating
                    FROM vw_ticket " . $filter->getWhere() .
                    " GROUP BY satisfaction_rating";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($filter->getParam());
            $result = $stmt->fetchAll();

            $ret['code'] = HC_SUCCESS;
            $ret['data'] = [];

            if(count($result) > 0){
                foreach($result as $reg){
                    $ret['data'][] =
                    [
                        'satisfaction_rating' => $reg['satisfaction_rating'],
                        'total' => $reg['total']
                    ];
                }
            }
        } catch (Exception $e) {
            $ret['code'] = HC_API_ERROR;
            $ret['error']['message'] = $e->getMessage();
        }

        return $response->withJson($ret)->withStatus($ret['code']);
    }

    /**
     * Syncronize ticket register in database with Zendesk Platform
     *
     * @param object $ticket
    */
    public function syncTicket($ticket) {
        $query = "SELECT IF(COUNT(1)>0, TRUE, FALSE) AS exist, updated_at FROM ticket WHERE api_id = :api_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':api_id' => $ticket->id]);
        $result = $stmt->fetchAll();

        $arrtags = [];
        foreach($ticket->tags as $tag) {
            $arrtags[] = $tag;
        }
        $tags = !empty($arrtags) ? implode(',', $arrtags) : null;

        $params = [
            ':api_id' => $ticket->id,
            ':requester_id' => $ticket->requester_id,
            ':organization_id' => $ticket->organization_id,
            ':satisfaction_rating' => $ticket->satisfaction_rating,
            ':assignee_id' => $ticket->assignee_id,
            ':subject' => $ticket->subject,
            ':description' => $ticket->description,
            ':priority' => $ticket->priority,
            ':status' => $ticket->status,
            ':type' => $ticket->type,
            ':created_at' => $ticket->created_at,
            ':updated_at' => $ticket->updated_at,
            ':tags' => $tags
        ];

        // If not exists insert a new ticket
        if($result[0]['exist'] === '0') {
            \App\V1\Tickets::ticketAdd($params);
        } else if(date_format(date_create($result[0]['updated_at']), 'YmdHis') !=
            date_format(date_create($ticket->updated_at), 'YmdHis')) {
            //Sync tickets verfying by updated_at
            \App\V1\Tickets::ticketUpd($params);
        }
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
                    updated_at,
                    requester_id,
                    organization_id,
                    satisfaction_rating,
                    tags
                ) VALUES (
                    :api_id,
                    :assignee_id,
                    :subject,
                    :description,
                    :priority,
                    :status,
                    :type,
                    :created_at,
                    :updated_at,
                    :requester_id,
                    :organization_id,
                    :satisfaction_rating,
                    :tags
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
                    updated_at = :updated_at,
                    requester_id = :requester_id,
                    organization_id = :organization_id,
                    satisfaction_rating = :satisfaction_rating,
                    tags = :tags
                WHERE api_id = :api_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }
}