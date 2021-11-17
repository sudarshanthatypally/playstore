<?php
class notificationIndexInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["text"] = array(
      "name"=>"text",
      "required"=>true,
      "description"=>"parameter description"
    );

    return $parameter;
  }
}
