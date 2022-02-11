<?php
class userValidateAccountInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["google_id"]= array(
      "name" => "google_id",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"google_id"
    ); 
    $parameter["gamecenter_id"]= array(
      "name" => "gamecenter_id",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"gamecenter_id"
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
