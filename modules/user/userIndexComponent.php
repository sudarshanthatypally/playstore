<?php
/**
 * Author : Abhijth Shetty
 * Date   : 05-02-2016
 * Desc   : This is a controller file for userIndex Component
 */
class userIndexComponent extends baseComponent
{
  public function execute()
  {
	  $this->includeJavascript('jquery-1.10.1.min.js,bootstrap.min.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css");

  }
}
