<?php
class userGoogleAlertInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    /*$parameter["type"] = array(
      "name" => "type",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "1- facebookAccount; 2- googleAccount; 3- Game Center"
    );*/

    $parameter["deviceToken"] = array(
      "name" => "device_token",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "device_token"
    );
    $parameter["platformId"] = array(
      "name" => "platform_id",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "platform_id: 1=google_id(android), 2=gamecenter_id(ios)"
    );
    /*$parameter["forceLink"] = array(
      "name" => "force_link",
      "type" => "text",
      "required" => false,
      "default" => "0",
      "description" => "1 - force_link"
    );*/

    return $parameter;
  }
}
