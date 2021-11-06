<?php
class cardPurchaseInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;
	
  public function getParameter()
  {
    $parameter = array();
    
    $parameter["masterCardId"] = array(
      "name"=>"master_card_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"purchasing card id"
    );

    $parameter["count"] = array(
      "name"=>"count",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"count of the card"
    );
    
    return $parameter;
  }
}