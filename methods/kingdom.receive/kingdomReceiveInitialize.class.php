<?php
class kingdomReceiveInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["kingdomId"] = array(
      "name"=>"kingdom_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"1",
      "description"=>"Kingdom Id for Message"
    );
    $parameter["lastMsgId"] = array(
      "name"=>"last_msg_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"1",
      "description"=>"Kingdom Message Id for Message"
    );
    return $parameter;
  }
}
