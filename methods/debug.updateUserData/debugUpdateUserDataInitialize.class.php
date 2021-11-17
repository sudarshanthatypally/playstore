<?php
class debugUpdateUserDataInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["relics"] = array(
      "name"=>"relics",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"relics"
    );

    $parameter["xp"] = array(
      "name"=>"xp",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"xp"
    );

    $parameter["crystal"] = array(
      "name"=>"crystal",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"crystal"
    );

    $parameter["circlet"] = array(
      "name"=>"circlet",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"circlet"
    );

    $parameter["gold"] = array(
      "name"=>"gold",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"gold"
    );

    $parameter["xp"] = array(
      "name"=>"xp",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"xp"
    );

    $parameter["levelId"] = array(
      "name"=>"level_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"level_id"
    );

    $parameter["masterStadiumId"] = array(
      "name"=>"master_stadium_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"master_stadium_id"
    );

    return $parameter;
  }
}
