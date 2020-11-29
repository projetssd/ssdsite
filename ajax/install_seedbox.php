<?php
require_once "../php/classes/service.php";
$utilisateur = $_GET['utilisateur'];
configure($utilisateur);
echo 'ok';
