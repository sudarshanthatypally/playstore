<?php
class userLeadershipBoardInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["fetchLimit"]= array(
      "name" => "fetchLimit",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"fetchLimit"
    ); 
    return $parameter;
  }
}
