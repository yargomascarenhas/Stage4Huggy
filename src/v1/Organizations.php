<?php

namespace App\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Exception;

final class Organizations{

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

}