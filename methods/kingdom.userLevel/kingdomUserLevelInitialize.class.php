<?php
class kingdomUserLevelInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["levelUserId"] = array(
      "name"=>"level_user_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"Level Type Change User Id"
    );
    $parameter["levelType"] = array(
      "name"=>"level_type",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"Level Type: 1 for promote, 2 for demote, 3 for kick off, 9 for kick off remove"
    );
    $parameter["kickMsg"] = array(
      "name"=>"kick_msg",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kick Message"
    );
/*
    $parameter["kingdomId"] = array(
      "name"=>"kingdom_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"Kingdom Id"
    );*/
    /*$parameter["kingdomName"] = array(
      "name"=>"kingdom_name",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"Kingdom Name"
    );

    $parameter["kingdomType"] = array(
      "name"=>"kingdom_type",
      "type"=>"text",
      "required"=>false,
      "default"=>"1",
      "description"=>"Default 1"
    );

    $parameter["kingdomLimit"] = array(
      "name"=>"kingdom_limit",
      "type"=>"text",
      "required"=>false,
      "default"=>"100",
      "description"=>"Kingdom Limit default is 100 users"
    );

    $parameter["shieldId"] = array(
      "name"=>"kingdom_shield_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"1",
      "description"=>"Kingdom Default Shield Id is 1"
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
      "name"=>"kingdom_req_cup_amt",
      "type"=>"text",
      "required"=>false,
      "default"=>"0",
      "description"=>"defaults is 0"
    );*/
    return $parameter;
  }
}
