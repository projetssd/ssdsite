<?php
require_once "../php/classes/utilisateur.php";

$utilisateur = $_GET['utilisateur'];
$passe = $_GET['passe'];
$email = $_GET['email'];
$domaine = $_GET['domaine'];
$idplex = $_GET['idplex'];
$passplex = $_GET['passplex'];
$idcloud = $_GET['idcloud'];
$passcloud = $_GET['passcloud'];
$idoauth = $_GET['idoauth'];
$clientoauth = $_GET['clientoauth'];
$mailoauth = $_GET['mailoauth'];

$user = new utilisateur($utilisateur);
$user->configure($passe, $email, $domaine, $idplex, $passplex, $idcloud, $passcloud, $idoauth, $clientoauth, $mailoauth);
echo 'ok';