<?php
class userBattleHistoryGamePlayInitialize extends baseInitialize{

  public $requestMethod = array("GET","POST");
  public $isSecured = false;
 
  public function getParameter()
  {
    $parameter = array();

    $parameter["roomId"] = array(
      "name" => "room_id",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "room_id"
    );

    $parameter["gameVideo"] = array(
      "name" => "game_video",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "game_video"
    );
    /*
    $parameter["gameVideo"] = array(
      "name" => "game_video",
      "type" => "file",
      "required" => false,
      "description" => "game video"
    );*/

    return $parameter;
  }
}
