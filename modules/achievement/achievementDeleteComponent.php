<?php
/**
 * Author : Abhijth Shetty
 * Date   : 17-01-2018
 * Desc   : This is a controller file for achievementDelete Component
 */
class achievementDeleteComponent extends baseComponent
{
  public function execute()
  {
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');

    if(isset($_GET['masterAchievementId']) && $_GET['masterAchievementId'] > 0){
      $achievementLib->deleteMasterAchievement($_GET['masterAchievementId']);
    }
    $this->redirectTo(getComponentUrl('achievement', 'list'));

  }
}
