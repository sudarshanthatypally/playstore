<?php
/**
 * Author : Abhijth Shetty
 * Date   : 12-04-2019
 * Desc   : This is a controller file for badgeDelete Component
 */
class badgeDeleteComponent extends baseComponent
{
  public function execute()
  {
    $badgeLib = autoload::loadLibrary('queryLib', 'badge');

    if(isset($_GET['masterBadgeId']) && $_GET['masterBadgeId'] > 0){
      $badgeLib->deleteMasterbadge($_GET['masterBadgeId']);
    }
    $this->redirectTo(getComponentUrl('badge', 'list'));
  }
}
