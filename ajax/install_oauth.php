<?php

require_once __DIR__ . '/../includes/header.php';

$clientoauth = $_POST['clientoauth'];
$secretoauth = $_POST['secretoauth'];
$mailoauth = $_POST['mailoauth'];

$google = new oauth($clientoauth);
$google->goauth($secretoauth, $mailoauth);
echo 'ok';
