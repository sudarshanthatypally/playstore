<?php
class roomCreateInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["roomType"] = array(
      "name"=>"room_type",
      "type"=>"text",
      "required"=>false,
      "default"=>"1",
      "description"=>"1.Normal, 2.Invite"
    );

    $parameter["inviteToken"] = array(
      "name"=>"invite_token",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"invite token recieved"
    );

    return $parameter;
  }
}
