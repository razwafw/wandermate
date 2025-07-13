<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    http_response_code(403);
    exit('Direct access not allowed.');
}

require_once 'env.php';

if (APP_ENV == 'production') {
    error_reporting(E_ERROR | E_PARSE);
} else if (APP_ENV == 'local') {
    error_reporting(E_ALL);
}