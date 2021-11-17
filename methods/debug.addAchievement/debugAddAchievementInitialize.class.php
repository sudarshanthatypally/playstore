<?php
class debugAddAchievementInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["masterAchievementId"] = array(
      "name"=>"master_chievement_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"master_chievement_id"
    );

    return $parameter;
  }
}
