<?php
require_once __DIR__ . '/../includes/header.php';

chdir(__DIR__ . '/../');

exec('/usr/bin/git pull',$return_lines,$return_code);
if($return_code != 0)
{
    echo json_encode(array("status" => 0,
                "message" => $return_lines));
}

$path_to_delete = array(
    __DIR__ . '/../cache',
    __DIR__ . '/../dist/js/min',
    __DIR__ . '/../dist/css/min'
    );
foreach($path_to_delete as $path)
{
    $files = glob($path . '/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
  }
}
}

echo json_encode(array("status" => 1));