<?php
class userDeleteInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["loginUserId"]= array(
      "name" => "login_user_id",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"login_user_id"
    ); 
    $parameter["isDel"]= array(
      "name" => "is_delete",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"is_delete"
    ); 
    /*
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
    $parameter["androidVerId"] = array(
      "name" => "android_version_id",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "android_version_id"
    );
    $parameter["iosVerId"] = array(
      "name" => "ios_version_id",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "ios_version_id"
    );*/
    return $parameter;
  }
}
