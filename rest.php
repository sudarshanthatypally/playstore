<?php
/* *
* Author: Sudarshan Thatypally
* Date : 10-09-2020
*/
//allowing Cross Domain Request
header("Access-Control-Allow-Origin: *");
require_once(dirname(__FILE__)."/base/autoload.class.php");
autoload::initRest(dirname(__FILE__));
$project = new executor();
$project->executeMethod();
echo $project->getResponse();
?>
