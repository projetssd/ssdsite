<?php
require_once __DIR__ . '/../includes/header.php';

exec(__DIR__.'/../scripts/maj_appli.sh', $output, $retval);

if($retval == 0)
{
    echo "ok";
}
else
{
    echo "bad";
}