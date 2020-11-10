<?php
require_once "../php/classes/service.php";
$service = new service($_GET['service']);
if($service->uninstall())
{
    echo "ok";
}
else
{
    echo "bad";
}