<?php
/**
 * Author : Abhijth Shetty
 * Date   : 22-01-2018
 * Desc   : This is a controller file for userLevelDelete Component
 */
class userLevelDeleteComponent extends baseComponent
{
  public function execute()
  {
    $masterLib = autoload::loadLibrary('queryLib', 'master');

    if(isset($_GET['masterLevelUpId']) && $_GET['masterLevelUpId'] > 0){
      $masterLib->deleteMasterLevelUp($_GET['masterLevelUpId']);
      $this->redirectTo(getComponentUrl('userLevel', 'listUserLevel'));
    }

  }
}
