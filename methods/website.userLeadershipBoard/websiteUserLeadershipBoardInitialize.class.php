<?php
class websiteUserLeadershipBoardInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = false;

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
