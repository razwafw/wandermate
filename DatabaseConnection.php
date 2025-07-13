<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    http_response_code(403);
    exit('Direct access not allowed.');
}

require_once 'env.php';

class DatabaseConnection extends mysqli
{
    public function __construct()
    {
        parent::__construct(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }
}