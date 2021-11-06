<?php
class userClaimKathikaInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    $parameter["kathikaId"] = array(
      "name" => "kathika_id",
      "type" => "text",
      "required" => true,
      "default" => "",
      "description" => "Kathika Id"
    );

    return $parameter;
  }
}
