<?php
require_once __DIR__ . '/../includes/header.php';

chdir(__DIR__ . '/../');

exec('/usr/bin/git pull',$return_lines,$return_code);
if($return_code != 0)
{
    return json_encode(array("status" => 0,
                "message" => $return_lines));
}
array_map( 'unlink', array_filter((array) glob(__DIR__ . '/../cache/*') ) );
array_map( 'unlink', array_filter((array) glob(__DIR__ . '/../dist/js/min/*') ) );
array_map( 'unlink', array_filter((array) glob(__DIR__ . '/../dist/css/min/*') ) );
return json_encode(array("status" => 1));