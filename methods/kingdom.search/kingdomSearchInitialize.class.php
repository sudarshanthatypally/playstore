<?php
class kingdomSearchInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();
    $parameter["searchName"] = array(
      "name"=>"search_name",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Search Name"
    );
    $parameter["kingdomType"] = array(
      "name"=>"kingdom_type",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom TYpe"
    );
    $parameter["requiredCups"] = array(
      "name"=>"kingdom_req_cup_amt",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Kingdom Required Cups Amount"
    );
    $parameter["reqWarrior"] = array(
      "name"=>"req_warrior",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Required Warrior"
    );
    return $parameter;
  }
}
