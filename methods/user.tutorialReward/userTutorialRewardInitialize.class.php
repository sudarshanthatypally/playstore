<?php
class userTutorialRewardInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["tutorialStatus"] = array(
      "name"=>"tutorial_status",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"tutorial_status"
    );

    $parameter["winStatus"] = array(
      "name"=>"win_status",
      "type"=>"text",
      "required"=>true,
      "default"=>"-1",
      "description"=>"1- Won; 2- Lost; 3- Draw"
    );

    return $parameter;
  }
}
