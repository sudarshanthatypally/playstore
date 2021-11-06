<?php
/**
 * Author : Abhijth Shetty
 * Date   : 24-04-2019
 * Desc   : This is a controller file for dailyRewardAdd Component
 */
class dailyRewardAddComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->card = $this->stadium = $this->stadiumList = $temp = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $dailyRewardLib = autoload::loadLibrary('queryLib', 'dailyReward');
    $inAppLib = autoload::loadLibrary('queryLib', 'inAppPurchase');

    $this->stadiumList = $masterLib->getMasterStadiumList();
    $this->masterCardList = $cardLib->getMasterCardList();
    $this->inventoryList = $inAppLib->getMasterInventoryList();
    $this->cubeList = array("Fire Cracker"=>CUBE_FIRECRACKER,"Bomb"=>CUBE_BOMB,"Rocket"=>CUBE_ROCKET);

    if(isPost())
    {
      $mandatoryFields = array('title' => $_POST['title'], 'crystal' => $_POST['crystal']);

      foreach($mandatoryFields as $field => $value )
      {
        if(isset($_POST[$field]) && $_POST[$field] == "" )
        {
          $this->result['status'] = true;
          $this->result['message'] = str_replace('_',' ',$field)." is mandatory";
          return false;
        }
      }

      $data = $itemData = array();
      $data['title'] = trim($_POST['title']);
      $data['crystal'] = trim($_POST['crystal']);

      if(!empty($_GET['masterDailyRewardId']))
      {
        $dailyRewardId = $_GET['masterDailyRewardId'];
        $dailyRewardLib->updateMasterDailyReward($_GET['masterDailyRewardId'], $data);
      } else {
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');
        $itemData['status'] = CONTENT_ACTIVE;
        $itemData['created_at'] = date('Y-m-d H:i:s');
        $dailyRewardId = $dailyRewardLib->insertMasterDailyReward($data);
 
        $itemData['master_daily_reward_id'] = $dailyRewardId;
        if(isset($_POST['master_inventory_id']))
        {
          $itemData['reward_item_id'] = $_POST['master_inventory_id'];
          $itemData['reward_type'] =DAILY_REWARD_TYPE_INVENTORY;
          $dailyRewardLib->insertMasterDailyRewardItem($itemData);
        }

        if(isset($_POST['master_card_id']))
        {
          $itemData['reward_item_id'] = $_POST['master_card_id'];
          $itemData['reward_type'] =DAILY_REWARD_TYPE_CARD;
          $dailyRewardLib->insertMasterDailyRewardItem($itemData);
        }

        if(isset($_POST['cube_id']))
        {
          $itemData['reward_item_id'] = $_POST['cube_id'];
          $itemData['reward_type'] =DAILY_REWARD_TYPE_CUBE;
          $dailyRewardLib->insertMasterDailyRewardItem($itemData);
        }
      }

      $this->redirectTo(getComponentUrl('dailyReward', 'list'));

    }
  }
}
