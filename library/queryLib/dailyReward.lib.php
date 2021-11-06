<?php
class dailyReward{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getDailyRewardList($options=array())
  {
    $sql = "SELECT *
            FROM daily_reward";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getDailyRewardDetail($dailyRewardId, $options=array())
  {
    $sql = "SELECT *
            FROM daily_reward
            WHERE daily_reward_id=:dailyRewardId";

    $result = database::doSelectOne($sql, array('dailyRewardId'=>$dailyRewardId));
    return $result;
  }

  public function insertDailyReward($options=array())
  {
    $sql = "INSERT INTO daily_reward ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function updateDailyReward($dailyRewardId, $options=array())
  {
    $sql = "UPDATE daily_reward SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE daily_reward_id =:dailyRewardId";
    $options['dailyRewardId'] = $dailyRewardId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function deleteDailyReward($dailyRewardId, $options=array())
  {
    $sql = "DELETE FROM daily_reward
            WHERE daily_reward_id = :dailyRewardId";

	  $result = database::doDelete($sql, array('dailyRewardId'=>$dailyRewardId));
    return $result;
  }

  public function getMasterDailyRewardList($options=array())
  {
    $sql = "SELECT *
            FROM master_daily_reward";

    $result = database::doSelect($sql);
    return $result;
  }

  public function updateMasterDailyReward($dailyRewardId, $options=array())
  {
    $sql = "UPDATE master_daily_reward SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE master_daily_reward_id =:dailyRewardId";
    $options['dailyRewardId'] = $dailyRewardId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function insertMasterDailyRewardItem($options=array())
  {
    $sql = "INSERT INTO master_daily_reward_item ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function insertMasterDailyReward($options=array())
  {
    $sql = "INSERT INTO master_daily_reward ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getUserDailySpecialOfferForGivenDay($userId, $options=array())
  {
    $sql = "SELECT *
            FROM user_daily_reward
            WHERE user_id = :userId
            ORDER BY created_at DESC";

    $result = database::doSelectOne($sql, array('userId'=>$userId));
    return $result;
  }

  public function getMasterDailyRewardDetail($rewardId, $options=array())
  {
    $sql = "SELECT *
            FROM master_daily_reward
            WHERE master_daily_reward_id =:rewardId";

    $result = database::doSelectOne($sql, array('rewardId' => $rewardId));
    return $result;
  }

  public function getMasterDailyRewardRandomly($stadiumId, $options=array())
  {
    $sql = "SELECT *
            FROM master_daily_reward
            WHERE master_stadium_id <=:stadiumId
            ORDER BY RAND()  
            LIMIT 1  ";

    $result = database::doSelectOne($sql, array('stadiumId' => $stadiumId));
    return $result;
  }

  public function insertUserDailyReward($options=array())
  {
    $sql = "INSERT INTO user_daily_reward ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getMasterDailyRewardItem($rewardId, $options=array())
  {
    $sql = "SELECT *
            FROM master_daily_reward_item
            WHERE master_daily_reward_id =:rewardId";

    $result = database::doSelect($sql, array('rewardId' => $rewardId));
    return $result;
  }

  public function getDailySpecialOfferDetails($dailySpecialOffer, $options=array())
  {
    $inventoryLib = autoload::loadLibrary('queryLib', 'inAppPurchase');
    $cardLib = autoload::loadLibrary('queryLib', 'card');

    $reward['daily_reward_id'] = $dailySpecialOffer['master_daily_reward_id'];
    $reward['title'] = $dailySpecialOffer['title'];
    $reward['cost'] = $dailySpecialOffer['amount'];
    $dailyRewardItem = $this->getMasterDailyRewardItem($dailySpecialOffer['master_daily_reward_id']);

    foreach($dailyRewardItem as $item) {
      $temp['master_id'] = $item['reward_item_id'];
      if($item['reward_type'] == DAILY_REWARD_TYPE_INVENTORY) {
        $rewardItem = $inventoryLib->getMasterInventoryDetail($item['reward_item_id']);
        $temp['title'] = $rewardItem['name'];
        $temp['type'] = ($rewardItem['type'] == CRYSTAL_INVENTORY) ? 'Crystal' : 'Gold';    
      } else if($item['reward_type'] == DAILY_REWARD_TYPE_CARD) {
        $rewardItem = $cardLib->getMasterCardDetail($item['reward_item_id']);
        $temp['title'] = $rewardItem['title'];
        $temp['type'] = 'Card'; 
      } else {
        $rewardItem = $inventoryLib->getMasterCubeInventoryDetail($item['reward_item_id']);
        $temp['title'] = CUBE_LIST[$rewardItem['cube_id']];
        $temp['type'] = 'Cube';    
      }
      $temp['count'] = $item['count'];
      $rewardItemValue[] = $temp;
    }
    $reward['items'] = $rewardItemValue;

    return $reward;

  }

  public function getUserDailySpecialOfferDetail($dailyRewardId,$options=array())
  {
    $sql = "SELECT *
            FROM user_daily_reward
            WHERE user_daily_reward_id =:dailyRewardId";

    $result = database::doSelectOne($sql, array('dailyRewardId'=>$dailyRewardId));
    return $result;
  }

  public function updateUserDailyReward($dailyRewardId, $options=array())
  {
    $sql = "UPDATE user_daily_reward SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_daily_reward_id =:dailyRewardId";
    $options['dailyRewardId'] = $dailyRewardId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
}
