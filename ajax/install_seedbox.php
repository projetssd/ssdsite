<?php
require_once "../php/classes/utilisateur.php";

$utilisateur = $_GET['utilisateur'];
$passe = $_GET['passe'];
$email = $_GET['email'];
$domaine = $_GET['domaine'];
$idplex = $_GET['idplex'];
$passplex = $_GET['passplex'];
$user = new utilisateur($utilisateur);
$user->configure($passe, $email, $domaine, $idplex, $passplex);
echo 'ok';