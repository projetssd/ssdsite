<?php
require_once __DIR__ . '/../includes/header.php';

chdir(__DIR__ . '/../');

exec('/usr/bin/git pull',$return_lines,$return_code);
if($return_code != 0)
{
    echo json_encode(array("status" => 0,
                "message" => $return_lines));
}

$log = new log;
foreach($return_lines as $line)
{
    $log->writelogappli($line,"gui","update");
}

exec('/usr/bin/composer install',$return_lines,$return_code);
if($return_code != 0)
{
    $log->writelogappli("Erreur sur la mise à jour !","gui","update");   
    echo json_encode(array("status" => 0,
                "message" => $return_lines));
    die('');
             
}
else{
    $log->writelogappli("Mise à jour terminée","gui","update");   
}


$log->writelogappli("Suppression des caches","gui","update"); 
$path_to_delete = array(
    __DIR__ . '/../cache',
    __DIR__ . '/../dist/js/min',
    //__DIR__ . '/../dist/css/min'
);
foreach ($path_to_delete as $path)
{
    $commande = 'rm -rf ' . $path . '/*';
    $log->writelogappli(shell_exec('rm -rf ' . $path . '/*'), "gui", "update");
}

$commande = 'rm -f ' . __DIR__ . '/../dist/css/min/*css';
$log->writelogappli(shell_exec($commande), "gui", "update");

echo json_encode(array("status" => 1));