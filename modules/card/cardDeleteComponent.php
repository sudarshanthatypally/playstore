<?php
/**
 * Author : Abhijth Shetty
 * Date   : 22-01-2018
 * Desc   : This is a controller file for cardDelete Component
 */
class cardDeleteComponent extends baseComponent
{
  public function execute()
  {
    $masterLib = autoload::loadLibrary('queryLib', 'master');

    if(isset($_GET['masterCardId']) && $_GET['masterCardId'] > 0)
    {
      $masterLib->deleteMasterCard($_GET['masterCardId']);
      $masterLib->deleteCardPropertyForMasterCard($_GET['masterCardId']);
      $masterLib->deleteCardPropertyLevelForMasterCard($_GET['masterCardId']);
    }

    $this->redirectTo(getComponentUrl('card', 'listMasterCard'));
  }
}
