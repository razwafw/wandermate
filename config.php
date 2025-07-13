<?php
require_once 'env.php';

if (APP_ENV == 'production') {
    error_reporting(E_ERROR | E_PARSE);
}