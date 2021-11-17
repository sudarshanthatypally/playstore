<?php
class userLoginInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = false;

  public function getParameter()
  {
    $parameter = array();


    $parameter["name"] = array(
      "name" => "name",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "name"
    );

    $parameter["deviceToken"] = array(
      "name" => "device_token",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "device_token"
    );

    $parameter["iosPushToken"] = array(
      "name" => "ios_push_token",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "ios_push_token"
    );

    $parameter["androidPushToken"] = array(
      "name" => "android_push_token",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "android_push_token"
    );

    return $parameter;
  }
}
