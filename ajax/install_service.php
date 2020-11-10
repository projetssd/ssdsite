<?php
require_once "../php/classes/service.php";
$service = new service($_GET['service']);
if($service->install())
{
    echo "ok";
}
else
{
    echo "bad";
}