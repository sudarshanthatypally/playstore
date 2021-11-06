<?php
class userLinkAccountInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["type"] = array(
      "name" => "type",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "1- facebookAccount; 2- googleAccount; 3- Game Center"
    );

    $parameter["accountId"] = array(
      "name" => "account_id",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "account_id"
    );

    $parameter["forceLink"] = array(
      "name" => "force_link",
      "type" => "text",
      "required" => false,
      "default" => "0",
      "description" => "1 - force_link"
    );

    return $parameter;
  }
}
