<?php
//echo $_SERVER['DOCUMENT_ROOT'].'/epiko_staging/methods';

require("../vendor/autoload.php");
$openapi = \OpenApi\scan("../methods");
header('Content-Type: application/jsom');
echo $openapi->toJson();