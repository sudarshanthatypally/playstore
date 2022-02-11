<?php
class userUpdateInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["name"] = array(
      "name" => "name",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "name"
    );

    $parameter["levelId"] = array(
      "name" => "level_id",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "level_id of the user"
    );

    $parameter["iosPushToken"] = array(
      "name" => "ios_push_token",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "ios_push_token"
    );

    $parameter["androidPushToken"] = array(
      "name" => "android_push_token",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "android_push_token"
    );

    $parameter["notificationStatus"] = array(
      "name" => "notification_status",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => " 1-Notification Active, 0-Notification inactive"
    );
    $parameter["isTutorialCompleted"] = array(
      "name" => "is_tutorial_completed",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "1 - Active"
    );
    $parameter["tutorial_seq"] = array(
      "name" => "tutorial_seq",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "tutorial_seq"
    );
    $parameter["editName_count"] = array(
      "name" => "editname_count",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "Name Count"
    );
    $parameter["kingQueen_status"] = array(
      "name" => "kingQueen_status",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "King / Queen Status"
    );
    $parameter["IOS_update"] = array(
      "name" => "IOS_update",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "IOS_update"
    );
    $parameter["android_update"] = array(
      "name" => "android_update",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "android_update"
    );
    $parameter["isKathikaTutorial"] = array(
      "name" => "isKathikaTutorial",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "isKathikaTutorial"
    );
    $parameter["isStorybookTutorial"] = array(
      "name" => "isStorybookTutorial",
      "type" => "text", 
      "required" => false,
      "default" => "",
      "description" => "isStorybookTutorial"
    );
    $parameter["editNameCost"] = array(
      "name" => "editname_cost",
      "type" => "text", 
      "required" => false,
      "default" => "",
      "description" => "editNameCost"
    );
    $parameter["isLogin"] = array(
      "name" => "is_login",
      "type" => "text", 
      "required" => false,
      "default" => "",
      "description" => "is_login"
    );
    return $parameter;
  }
}
