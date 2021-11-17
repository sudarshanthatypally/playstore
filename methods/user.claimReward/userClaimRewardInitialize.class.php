<?php
class userClaimRewardInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["userRewardId"] = array(
      "name"=>"user_reward_id",
      "type"=>"text",
      "required"=> true,
      "default"=>"",
      "description"=>"user_reward_id"
    );

    $parameter["claimReward"] = array(
      "name"=>"claim_reward",
      "type"=>"text",
      "required"=>false,
      "default"=>"0",
      "description"=>"1-Claim"
    ); 

    $parameter["cubeUpgradeId"]= array(
      "name" => "cube_upgrade_id",
      "type"=>"text",
      "required"=> false,
      "default"=>"",
      "description"=>"cube_upgrade_id"
    ); 
    $parameter["androidVerId"] = array(
      "name" => "android_version_id",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "android_version_id"
    );
    $parameter["iosVerId"] = array(
      "name" => "ios_version_id",
      "type" => "text",
      "required" => false,
      "default" => "",
      "description" => "ios_version_id"
    );
    return $parameter;
  }
}
