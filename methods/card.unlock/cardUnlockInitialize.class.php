<?php
class cardUnlockInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["masterCardId"] = array(
      "name" => "master_card_id",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "master Card Id"
    );

    return $parameter;
  }
}
