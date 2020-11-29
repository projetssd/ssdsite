<?php
require_once "../php/classes/service.php";
$utilisateur = $_GET['utilisateur'];
$user = new utilisateur($utilisateur);
$user->configure();
echo 'ok';
