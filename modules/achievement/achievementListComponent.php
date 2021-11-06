<?php
/**
 * Author : Abhijth Shetty
 * Date   : 17-01-2018
 * Desc   : This is a controller file for achievementList Component
 */
class achievementListComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->achievementList = array();

    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');

    $achievementList = $achievementLib->getMasterAchievementList();

    foreach ($achievementList as $achievement)
    {
      $temp = array();
      $temp['master_achievement_id'] = $achievement['master_achievement_id'];
      $temp['title'] = $achievement['title'];
      $temp['description'] = $achievement['description'];
      $temp['achievement_type'] = $achievement['achievement_type'];
      $temp['xp'] = $achievement['xp'];
      $temp['count'] = $achievement['count'];
      $temp['cube_id'] = $achievement['cube_id'];

      $temp['status'] = 1;

      $this->achievementList[] = $temp;
    }
  }
}
