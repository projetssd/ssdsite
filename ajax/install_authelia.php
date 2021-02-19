<?php

require_once __DIR__ . '/../includes/header.php';

$mailauthelia = $_POST['mailauthelia'];
$smtpauthelia = $_POST['smtpauthelia'];
$portauthelia = $_POST['portauthelia'];
$passeauthelia = $_POST['passeauthelia'];

$authentification = new authelia($mailauthelia);
$authentification->add_authelia($smtpauthelia, $portauthelia, $passeauthelia);
echo 'ok';
