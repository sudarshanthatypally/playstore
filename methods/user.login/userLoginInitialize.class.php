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
      "required" => false,
      "default" => "",
      "description" => "device_token"
    );

    $parameter["googleId"] = array(
      "name" => "googleid",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "googleid"
    );
    $parameter["gamecenterId"] = array(
      "name" => "gamecentreid",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "gamecentreid"
    );
    $parameter["platformId"] = array(
      "name" => "platform_id",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "platform_id: 1=google_id(android), 2=gamecenter_id(ios)"
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
