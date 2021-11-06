<?php
class kingdomNotificationInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["lastNotificationId"] = array(
      "name"=>"last_notification_id",
      "type"=>"text",
      "required"=>false,
      "default"=>"0",
      "description"=>"last_notification_id"
    );

    $parameter["limit"] = array(
      "name"=>"limit",
      "type"=>"text",
      "required"=>false,
      "default"=>"10",
      "description"=>"limit"
    );

    return $parameter;
  }
}
