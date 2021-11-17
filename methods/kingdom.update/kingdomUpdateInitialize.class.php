<?php
class kingdomUpdateInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["kingdomId"] = array(
      "name"=>"kingdom_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"Kingdom Id"
    );
    $parameter["kingdomName"] = array(
      "name"=>"kingdom_name",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Name"
    );

    $parameter["kingdomType"] = array(
      "name"=>"kingdom_type",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Type"
    );

    $parameter["kingdomLimit"] = array(
      "name"=>"kingdom_limit",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Limit"
    );

    $parameter["shieldId"] = array(
      "name"=>"kingdom_shield_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Shield Id"
    );

    $parameter["kingdomLocation"] = array(
      "name"=>"kingdom_location",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Location"
    );

    $parameter["kingdomDesc"] = array(
      "name"=>"kingdom_desc",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Description"
    );
    
    $parameter["kingdomRequiedCups"] = array(
      "name"=>"kingdom_req_cups",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"defaults is 0"
          );
    return $parameter;
  }
}
