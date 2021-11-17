<?php
class kingdomGetDetailInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["kingdomId"]= array(
      "name" => "kingdom_id",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"kingdom id"
    ); 
    return $parameter;
  }
}
 