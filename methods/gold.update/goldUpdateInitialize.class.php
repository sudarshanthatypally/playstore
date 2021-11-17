<?php
class goldUpdateInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["masterGoldInventoryId"] = array(
      "name"=>"master_gold_inventory_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"master_gold_inventory_id"
    );

    return $parameter;
  }
}
