<?php
class userGetRewardListInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["userRewardtype"] = array(
      "name"=>"user_reward_type",
      "type"=>"text",
      "required"=>true,
      "default"=>"1",
      "description"=>"1-Cube earned during match."
    );

    return $parameter;
  }
}
