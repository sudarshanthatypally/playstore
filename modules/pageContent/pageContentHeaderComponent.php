<?php
/**
 * Author : Abhijth Shetty
 * Date   : 21-03-2015
 * Desc   : This is a controller file for pageContentHeader Component
 */
class pageContentHeaderComponent extends baseComponent
{
  public function execute()
  {
    $this->page = (isset($_GET['module']))?$_GET['module']:'index';
  }
}
