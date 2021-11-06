<?php
class userGetDetailInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["levelUp"]= array(
      "name" => "level_up",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"level_up"
    ); 
    $parameter["stadiumlevelUp"]= array(
      "name" => "stadium_level_up",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"stadium_level_up"
    ); 
    return $parameter;
  }
}
