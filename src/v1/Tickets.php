<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Exception;

final class Tickets{

    public function __construct($pdo){
        $this->pdo = $pdo;
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
            $this->ticketAdd($params);
        } else if(date_format(date_create($result[0]['updated_at']), 'YmdHis') !=
            date_format(date_create($ticket->updated_at), 'YmdHis')) {
            //Sync tickets verfying by updated_at
            $this->ticketUpd($params);
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