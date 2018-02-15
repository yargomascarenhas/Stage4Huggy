<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Lib\PDOFilter as Filter;
use \Lib\Pagination as Pagination;
use Exception;

final class Tickets extends Pagination{
    private $defaultSort = '-id';
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
                        FROM ticket " . $filter->getWhere();
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
                        'subject' => $reg['subject'],
                        'description' => $reg['description'],
                        'priority' => $reg['priority'],
                        'status' => $reg['status'],
                        'type' => $reg['type'],
                        'created_at' => $reg['created_at'],
                        'updated_at' => $reg['updated_at'],
                        'requester_id' => $reg['requester_id'],
                        'organization_id' => $reg['organization_id'],
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

        $id = $request->getAttribute('route')->getArgument('id');
        if(empty($id)){
            $sort = empty($queryParams['sort']) ? $this->defaultSort : $queryParams['sort'];
            $filter->setOrder(parent::getOrder($sort, $this->dictonary));

            if(!empty($queryParams['description'])){
                $filter->addFilter('AND description = :description', array(':description' => $queryParams['description']));
            }
        } else {
            $filter->addFilter('AND id = :id', array(':id' => $id));
        }

        $ret = $this->select($filter, $request);
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