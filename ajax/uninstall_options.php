<?php

require_once __DIR__ . '/../includes/header.php';

$outils = $_GET['outils'];
$scanner = new options($outils);
$scanner->uninstall_tools();
echo 'ok';
