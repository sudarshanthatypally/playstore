<?php
/**
 * Author : Abhijth Shetty
 * Date   : 22-01-2018
 * Desc   : This is a controller file for cardLevelDelete Component
 */
class cardLevelDeleteComponent extends baseComponent
{
  public function execute()
  {
    $masterLib = autoload::loadLibrary('queryLib', 'master');

    if(isset($_GET['masterCardLevelUpgradeId']) && $_GET['masterCardLevelUpgradeId'] > 0){
      $masterLib->deleteMasterCardLevelUpgrade($_GET['masterCardLevelUpgradeId']);
    }

    $this->redirectTo(getComponentUrl('cardLevel', 'listCardLevel'));
  }
}
