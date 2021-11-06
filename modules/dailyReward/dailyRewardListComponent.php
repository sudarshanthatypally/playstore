<?php
/**
 * Author : Abhijth Shetty
 * Date   : 24-04-2019
 * Desc   : This is a controller file for dailyRewardList Component
 */
class dailyRewardListComponent extends baseComponent
{
  public function execute()
  {
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->cardList = array();

    $dailyRewardLib = autoload::loadLibrary('queryLib', 'dailyReward');

    $dailyRewardList = $dailyRewardLib->getMasterDailyRewardList();

    foreach ($dailyRewardList as $dailyReward)
    {
      $temp = array();
      $temp['master_daily_reward_id'] = $dailyReward['master_daily_reward_id'];
      $temp['title'] = $dailyReward['title'];
      $temp['crystal'] = $dailyReward['crystal'];
      $temp['status'] = 1;

      $this->rewardList[] = $temp;
    }
  }
}
