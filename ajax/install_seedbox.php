<?php
require_once __DIR__ . '/../includes/header.php';

$utilisateur = $_GET['utilisateur'];
$passe       = $_GET['passe'];
$email       = $_GET['email'];
$domaine     = $_GET['domaine'];
$idplex      = $_GET['idplex'];
$passplex    = $_GET['passplex'];
$idcloud     = $_GET['idcloud'];
$passcloud   = $_GET['passcloud'];
$idoauth     = $_GET['idoauth'];
$clientoauth = $_GET['clientoauth'];
$mailoauth   = $_GET['mailoauth'];


$user = new utilisateur($utilisateur);
$user->configure($passe, $email, $domaine, $idplex, $passplex, $idcloud, $passcloud, $idoauth, $clientoauth, $mailoauth);

// on n'affiche rien, c'est géré par la fonction
//echo 'ok';