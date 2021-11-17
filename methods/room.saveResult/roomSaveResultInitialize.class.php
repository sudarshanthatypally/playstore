<?php
class roomSaveResultInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["roomId"] = array(
      "name"=>"room_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"room_id"
    );

    $parameter["winStatus"] = array(
      "name"=>"win_status",
      "type"=>"text",
      "required"=>true,
      "default"=>"-1",
      "description"=>"1- Won; 2- Lost; 3- Draw"
    );

    $parameter["opponentId"] = array(
      "name"=>"opponent_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"-1",
      "description"=>"opponent_id"
    );

    $parameter["circlet"] = array(
      "name"=>"circlet",
      "type"=>"text",
      "required"=>false,
      "default"=>"0",
      "description"=>"circlet"
    );
    $parameter["opponent_circlet"] = array(
      "name"=>"opponent_circlet",
      "type"=>"text",
      "required"=>false,
      "default"=>"0",
      "description"=>"opponent_circlet"
    );

    $parameter["battle_opp_id"] = array(
      "name"=>"battle_opp_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"0",
      "description"=>"battle_opp_id"
    );
    $parameter["room_type"] = array(
      "name"=>"room_type",
      "type"=>"text",
      "required"=>false,
      "default"=>"0",
      "description"=>"room_type"
    ); 
    return $parameter;
  }
}
