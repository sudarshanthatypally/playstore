<?php
class debugAddCubeInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;
	
  public function getParameter()
  {
    $parameter = array();
    
    $parameter["cubeType"] = array(
      "name"=>"cube_type",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>"1. titanium, 2. diamond, 3. platinum"
    );
    
    return $parameter;
  }
}