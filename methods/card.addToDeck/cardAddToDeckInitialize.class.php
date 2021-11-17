<?php
class cardAddToDeckInitialize extends baseInitialize{

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

    $parameter["replaceWithCard"] = array(
      "name" => "replace_with_card",
      "type" => "text",
      "required" => false,
      "default" => "0",
      "description" => "replace_with_card"
    );

    return $parameter;
  }
}
