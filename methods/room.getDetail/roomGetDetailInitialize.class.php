<?php
class roomGetDetailInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["type"] = array(
      "name" => "type",
      "type" => "text",
      "required" => false,
      "default" => "1",
      "description" => "1-Search;2-Cancle"
    );
    $parameter["match_type"] = array(
      "name" => "match_type",
      "type" => "text",
      "required" => false,
      "default" => "1",
      "description" => "1-Normal;2-Invite"
    );
    $parameter["waitingRoomId"] = array(
      "name" => "waiting_room_id",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "waiting_room_id"
    );

    return $parameter;
  }
}
