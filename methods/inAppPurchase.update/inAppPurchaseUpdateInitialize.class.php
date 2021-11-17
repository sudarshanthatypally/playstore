<?php
class inAppPurchaseUpdateInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["crystal"] = array(
      "name"=>"crystal",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"crystal"
    );

    $parameter["data"] = array(
      "name"=>"data",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"data"
    );

    return $parameter;
  }
}
