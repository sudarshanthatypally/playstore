<?php
class kingdomSendInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["senderId"] = array(
      "name"=>"sender_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"1",
      "description"=>"Kingdom Message Sender ID"
    );
    $parameter["receiverId"] = array(
      "name"=>"receiver_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"1",
      "description"=>"Kingdom Message Receiver ID"
    );
    $parameter["kingdomId"] = array(
      "name"=>"kingdom_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"1",
      "description"=>"Kingdom Id for Message"
    );
    $parameter["kingdomMsgType"] = array(
      "name"=>"kingdom_msg_type",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Message Type"
    );
    $parameter["kingdomChatType"] = array(
      "name"=>"kingdom_chat_type",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Chat Type"
    ); 
    $parameter["battleType"] = array(
      "name"=>"battle_type",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Battle Type"
    ); 
    $parameter["battleState"] = array(
      "name"=>"battle_state",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Battle State"
    ); 
    $parameter["kingdomMsg"] = array(
      "name"=>"kingdom_msg",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Message"
    );
    return $parameter;
  }
}
