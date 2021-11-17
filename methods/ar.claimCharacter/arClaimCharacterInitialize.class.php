<?php
class arClaimCharacterInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["characterId"] = array(
      "name" => "character_id",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "Character Id"
    );

    return $parameter;
  }
}
