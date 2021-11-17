<?php
class dailyRewardClaimInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;
	
  public function getParameter()
  {
    $parameter = array();
    
    $parameter["dailyRewardId"] = array(
      "name"=>"daily_reward_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"daily reward id"
    );
    
    return $parameter;
  }
}