<?php
/**
 * Author : Abhijth Shetty
 * Date   : 20-01-2018
 * Desc   : This is a controller file for cardLevelListCardLevel Component
 */
class cardLevelListCardLevelComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->userLevel =  $temp = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $this->cardLevelList = $masterLib->getMasterCardLevelUpgradeList();
  }
}
