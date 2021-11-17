<?php
/**
 * Author : Abhijth Shetty
 * Date   : 22-01-2018
 * Desc   : This is a controller file for stadiumDelete Component
 */
class stadiumDeleteComponent extends baseComponent
{
  public function execute()
  {
    $masterLib = autoload::loadLibrary('queryLib', 'master');

    if(isset($_GET['stadiumId']) && $_GET['stadiumId'] > 0){
      $masterLib->deleteMasterStadium($_GET['stadiumId']);
    }

    $this->redirectTo(getComponentUrl('stadium', 'listStadium'));
  }
}
