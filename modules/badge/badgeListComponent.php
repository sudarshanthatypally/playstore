<?php
/**
 * Author : Abhijth Shetty
 * Date   : 12-04-2019
 * Desc   : This is a controller file for badgeList Component
 */
class badgeListComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->badgeList = array();

    $badgeLib = autoload::loadLibrary('queryLib', 'badge');

    $badgeList = $badgeLib->getMasterBadgeList();

    foreach ($badgeList as $badge)
    {
      $temp = array();
      $temp['master_badge_id'] = $badge['master_badge_id'];
      $temp['title'] = $badge['title'];
      $temp['min_relic_count'] = $badge['min_relic_count'];
      $temp['max_relic_count'] = $badge['max_relic_count'];
      $temp['status'] = $badge['status'];

      $this->badgeList[] = $temp;
    }
  }
}
