<?php

/**
 * Author : Sudarshan Thatypally
 * Date   : 04-09-2020
 * Desc   : This is a controller file for characterList Action 
 */

class arCharacterDataInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;

  public function getParameter()
  {
    $parameter = array();

    return $parameter;
  }
}
