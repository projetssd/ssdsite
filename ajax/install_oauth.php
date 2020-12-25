<?php

require_once __DIR__ . '/../includes/header.php';

$clientoauth = $_GET['clientoauth'];
$secretoauth = $_GET['secretoauth'];
$mailoauth = $_GET['mailoauth'];

$google = new oauth($clientoauth);
$google->oauth($secretoauth, $mailoauth);
echo 'ok';
