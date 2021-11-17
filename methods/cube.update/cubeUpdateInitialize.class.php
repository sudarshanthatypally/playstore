<?php
class cubeUpdateInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["masterCubeInventoryId"] = array(
      "name"=>"master_cube_inventory_id",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"master_cube_inventory_id"
    );

    return $parameter;
  }
}
