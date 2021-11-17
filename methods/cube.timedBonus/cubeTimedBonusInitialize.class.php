<?php
class cubeTimedBonusInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();


    $parameter["userRewardtype"] = array(
      "name"=>"user_reward_type",
      "type"=>"text",
      "required"=>true,
      "default"=>"4",
      "description"=>"4-Copper cube; 5-Bronze cube."
    );

    return $parameter;
  }
}
