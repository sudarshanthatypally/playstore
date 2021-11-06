<?php
class logsSaveInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = false;

  public function getParameter()
  {
    $parameter = array();
    $parameter["apiName"] = array(
      "name"=>"api_name",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"api Name"
    );
    $parameter["errorLevel"] = array(
      "name"=>"error_level",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"e=>Error, i=> Info, d=>Debug"
    );
    $parameter["logMessage"] = array(
      "name"=>"log_message",
      "type"=>"text",
      "required"=>false,
      "default"=>"",
      "description"=>"Log Message"
    );

    return $parameter;
  }
}
