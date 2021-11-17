<?php
class cardGetMasterListInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
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
    );
    return $parameter;
  }
}
