<?php
require_once __DIR__ . '/../includes/header.php';

$git = new git;
$test_maj = $git->test_update();

echo json_encode($test_maj);

if($mode_debug)
{
    $debugbar->sendDataInHeaders();
    $debugbar['messages']->addMessage("On est dans disque.php ");
}