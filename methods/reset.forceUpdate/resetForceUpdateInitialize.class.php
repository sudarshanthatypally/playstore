<?php
class resetForceUpdateInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["android_update"] = array(
      "name"=>"android_update",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"android_update"
    );   
    $parameter["IOS_update"] = array(
      "name"=>"IOS_update",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"IOS_update"
    );   
    return $parameter;
  }
}
