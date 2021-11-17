<?php
class cardLevelUpInitialize extends baseInitialize{

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
      "description" => "master_card_id"
    );

    return $parameter;
  }
}
