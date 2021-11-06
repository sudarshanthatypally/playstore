<?php
/**
 * Author : Abhijth Shetty
 * Date   : 10-02-2018
 * Desc   : This is a controller file for cardDeleteCardPropertyLevel Component
 */
class cardDeleteCardPropertyLevelComponent extends baseComponent
{
  public function execute()
  {
    $masterLib = autoload::loadLibrary('queryLib', 'master');

    if(isset($_GET['cardPropertyLevelUpgradeId']) && $_GET['cardPropertyLevelUpgradeId'] > 0){
      $masterLib->deleteCardPropertyLevel($_GET['cardPropertyLevelUpgradeId']);
    }
    $this->redirectTo(getComponentUrl('card', 'listCardPropertyLevel', array('cardPropertyId' => $_GET['cardPropertyId'], 'masterCardId' => $_GET['masterCardId'])));
  }
}
