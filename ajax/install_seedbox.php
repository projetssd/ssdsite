<?php
require_once __DIR__ . '/../includes/header.php';

$utilisateur = addslashes($_GET['utilisateur']);
$passe       = addslashes($_GET['passe']);
$email       = addslashes($_GET['email']);
$domaine     = addslashes($_GET['domaine']);
$idplex      = addslashes($_GET['idplex']);
$passplex    = addslashes($_GET['passplex']);
$idcloud     = addslashes($_GET['idcloud']);
$passcloud   = addslashes($_GET['passcloud']);
$idoauth     = addslashes($_GET['idoauth']);
$clientoauth = addslashes($_GET['clientoauth']);
$mailoauth   = addslashes($_GET['mailoauth']);


$user = new utilisateur($utilisateur);
$user->configure($passe, $email, $domaine, $idplex, $passplex, $idcloud, $passcloud, $idoauth, $clientoauth, $mailoauth);

// on n'affiche rien, c'est géré par la fonction
//echo 'ok';