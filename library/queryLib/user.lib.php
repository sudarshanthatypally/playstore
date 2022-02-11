<?php
class user{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getUserList($options=array())
  {
    $sql = "SELECT *
            FROM user";

    $result = database::doSelect($sql);
    return $result;
  }
  public function getServerConfig($options=array())
  {
    $sql = "SELECT *
            FROM server_config";

    $result = database::doSelectOne($sql);
    return $result;
  }
  public function getUserDetail($userId, $options=array())
  {
    $sql = "SELECT *
            FROM user
            WHERE user_id = :userId";

    $result = database::doSelectOne($sql, array('userId'=>$userId));
    return $result;
  }
  public function getUserSameLoginDetail($deviceToken, $options=array())
  {
    $sql = "SELECT *
            FROM user
            WHERE device_token LIKE '".$deviceToken."%'
            ORDER BY last_access_time ASC";

    $result = database::doSelect($sql);//, array('deviceToken'=>$deviceToken)
    return $result;
  }
  public function getUserSameLoginDetailWithPlatform($deviceToken, $platformId, $options=array())
  {
    if($platformId==1){
      $sql = "SELECT *
            FROM user
            WHERE device_token LIKE '".$deviceToken."%' AND game_center_id=''
            ORDER BY last_access_time ASC";
    }elseif($platformId==2){
      $sql = "SELECT *
            FROM user
            WHERE device_token LIKE '".$deviceToken."%' AND google_id=''
            ORDER BY last_access_time ASC";
    }else{
      $sql = "SELECT *
            FROM user
            WHERE device_token LIKE '".$deviceToken."%'
            ORDER BY last_access_time ASC";
    }
    

    $result = database::doSelect($sql);//, array('deviceToken'=>$deviceToken)
    return $result;
  }

  public function getUserDetailsOnRelics($options=array()){
    $sql ="SELECT @a:=@a+1 srno, u.*
            FROM user u,(SELECT @a:= 0) AS a
            WHERE u.is_ai!=1
            ORDER BY u.relics DESC
            LIMIT 100"; 
    $result = database::doSelect($sql);
    return $result;
  }
  public function getUserLevelDetails()
  {
    $sqlxp = "SELECT *
            FROM master_level_up";

    $resultxp = database::doSelectOne($sql);
  }
  public function getBadgeByUserRelics($relics, $options=array())
  {
    $sql = "SELECT * 
    FROM master_badge 
    WHERE min_relic_count<=:relics 
    ORDER BY master_badge_id DESC 
    LIMIT 1";

    $result = database::doSelectOne($sql, array('relics'=>$relics));
    return $result;
  }
  
  public function getUserCrystalDetail($userCubeId, $stadiumId, $options = array())
  {
    $sql = "SELECT *
            FROM master_cube_reward
            WHERE cube_id = :userCubeId AND master_stadium_id = :stadiumId";

    $result = database::doSelectOne($sql, array('cubeId' => $userCubeId, 'stadiumId' => $stadiumId));
    return $result;
  }
  public function insertUser($options=array())
  {
    $sql = "INSERT INTO user ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function insertEditName($options=array())
  {
    $sql = "INSERT INTO editName_inventory ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function getEditNameClaimed($userId, $dt){
    date_default_timezone_set('Asia/Kolkata');
    $sql = "SELECT * 
            FROM editName_inventory eni
            WHERE user_id=:userId AND eni.time > '".$dt."'- INTERVAL 1 DAY
            ORDER BY eni.editname_id DESC";
  
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function insertBattleHistory($options=array())
  {
    $sql = "INSERT INTO battle_history";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function insertFriendlyBattleHistory($options=array())
  {
    $sql = "INSERT INTO friendly_battle_history";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function getBattleHistoryList($userId, $options=array())
  {
    $sql = "SELECT *
            FROM battle_history
            WHERE user_id=:userId
            ORDER BY created_at DESC";

    $result = database::doSelect($sql, array('userId'=>$userId));
    return $result;
  }
  public function getFriendlyBattleHistoryList($userId, $roomId, $options=array())
  {
    $sql = "SELECT *
            FROM friendly_battle_history
            WHERE user_id=:userId AND room_id=:roomId
            ORDER BY created_at DESC
            LIMIT 1";

    $result = database::doSelect($sql, array('userId'=>$userId, 'roomId'=>$roomId));
    return $result;
  }
  public function getFriendlyBattleHistoryWithRoomList($roomId, $options=array())
  {
    $sql = "SELECT *
            FROM friendly_battle_history
            WHERE room_id=:roomId
            ORDER BY created_at DESC
            LIMIT 1";

    $result = database::doSelectOne($sql, array('roomId'=>$roomId));
    return $result;
  }
  public function getCheckRoomIdInFriendlyBattleHistoryList($roomId, $options=array())
  {
    $sql = "SELECT *
            FROM friendly_battle_history
            WHERE room_id=:roomId";

    $result = database::doSelectOne($sql, array('roomId'=>$roomId));
    return $result;
  }
  public function getCheckRoomIdInmsgLst($roomId, $options=array())
  {
    $sql = "SELECT *
            FROM kingdom_messages 
            WHERE room_id=:roomId AND battle_state=3";  

    $result = database::doSelectOne($sql, array('roomId'=>$roomId));
    return $result;
  }
  public function updateUser($userId, $options=array())
  {
    $sql = "UPDATE user SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_id =:userId";
    $options['userId'] = $userId;

    $result = database::doUpdate($sql, $options);

    return $result;
  }
  public function updateFbUser($fbId, $options=array())
  {
    $sql = "UPDATE user SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE facebook_id =:fbId";
    $options['fbId'] = $fbId;

    $result = database::doUpdate($sql, $options);

    return $result;
  }

  public function resetForceUpdateAndroid($options=array())
  {
    $sql = "UPDATE user SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= ".$value.", ";
    }
    $sql = rtrim($sql, ", ");

    $result = database::doUpdate($sql);

    return $result;
  }
  
  public function getUserForName($name, $options = array())
  {
    $sql = "SELECT *
            FROM user
            WHERE name = :name";

    $result = database::doSelectOne($sql, array('name' => $name));
    return $result;
  }

  public function getUserForDeviceToken($deviceToken, $options = array())
  {
    $sql = "SELECT *
            FROM user
            WHERE device_token = :deviceToken";

    $result = database::doSelectOne($sql, array('deviceToken' => $deviceToken));
    return $result;
  }

  public function getUserForFbAccount($accountId, $options = array())
  {
    $sql = "SELECT *
            FROM user
            WHERE fb_access_token = :accountId";

    $result = database::doSelectOne($sql, array('accountId' => $accountId));
    return $result;
  }

  public function processRegistration($userId)
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    date_default_timezone_set('Asia/Kolkata');
    $defaultTowerDetail = $cardLib->getMasterLevelUpXpDetail(DEFAULT_USER_LEVEL_ID);

    //default values
    $userLib->updateUser($userId, array('level_id' => DEFAULT_USER_LEVEL_ID,
                                        'notification_status' => CONTENT_ACTIVE,
                                        'god_tower_health' => $defaultTowerDetail['god_tower_health'],
                                        'stadium_tower_damage' => $defaultTowerDetail['stadium_tower_damage'],
                                        'god_tower_damage' => $defaultTowerDetail['god_tower_damage'],
                                        'is_tutorial_completed' => CONTENT_INACTIVE,
                                        'stadium_tower_health' => $defaultTowerDetail['stadium_tower_health'],
                                        'is_copper_cube_notification_sent' => CONTENT_ACTIVE,
                                        'gold' => DEFAULT_GOLD,
                                        'crystal' => 0));
//DEFAULT_CRYSTAL
    $defaultCardList = $cardLib->getDefaultMasterCardList();
    $lmt = 0;
    foreach($defaultCardList as $defaultCard)
    {
      $userCard = $cardLib->getUserCardForUserIdAndMasterCardId($userId, $defaultCard['master_card_id']);
      if(empty($userCard))
      { 
        $lmt_val = ($lmt<=7)?CONTENT_ACTIVE:CONTENT_INACTIVE;
        $userCardLevel = $cardLib->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($defaultCard['master_card_id']);
        $levelId=(empty($userCardLevel['level_id']))?DEFAULT_CARD_LEVEL_ID:$userCardLevel["level_id"];
        $userCardId = $cardLib->insertUserCard(array(
                      'user_id' => $userId,
                      'master_card_id' => $defaultCard['master_card_id'],
                      'is_deck' => $lmt_val,
                      'level_id' => $levelId,
                      'user_card_count' => DEFAULT_CARD_COUNT,
                      'created_at' => date('Y-m-d H:i:s'),
                      'status' => CONTENT_ACTIVE ));

        $cardPropertyList = $cardLib->getMasterCardPropertyList($defaultCard['master_card_id']);
        //print_log("Master Id:".$defaultCard['master_card_id']."   level:".$levelId);
        foreach($cardPropertyList as $cardProperty)
        {
          /*if($defaultCard['master_card_id']==7){
            $cardPropertyValue = $cardLib->getCardPropertyValue($defaultCard['master_card_id'], $levelId, $cardProperty['card_property_id']);
          }
          else{
            $cardPropertyValue = $cardLib->getCardPropertyValue($defaultCard['master_card_id'], DEFAULT_CARD_LEVEL_ID, $cardProperty['card_property_id']);
          }*/
          $cardPropertyValue = $cardLib->getCardPropertyValue($defaultCard['master_card_id'], $levelId, $cardProperty['card_property_id']);
          $cardLib->insertUserCardProperty(array(
                          'user_id' => $userId,
                          'card_property_id' => $cardProperty['card_property_id'],
                          'user_card_id' => $userCardId,
                          'user_card_property_value' => $cardPropertyValue['card_property_value'],
                          'created_at' => date('Y-m-d H:i:s'),
                          'status' => CONTENT_ACTIVE));
        }
      }
      $lmt++;
    }


    // $defaultCardList = $cardLib->getMasterCardList();
    // foreach($defaultCardList as $defaultCard)
    // {
    //   $userCard = $cardLib->getUserCardForUserIdAndMasterCardId($userId, $defaultCard['master_card_id']);
    //   if(empty($userCard))
    //   {
    //     $userCardId = $cardLib->insertUserCard(array(
    //                   'user_id' => $userId,
    //                   'master_card_id' => $defaultCard['master_card_id'],
    //                   'is_deck' => ($defaultCard['is_card_default'] == CONTENT_ACTIVE)?CONTENT_ACTIVE:CONTENT_INACTIVE,
    //                   'level_id' => DEFAULT_CARD_LEVEL_ID,
    //                   'user_card_count' => DEFAULT_CARD_COUNT,
    //                   'created_at' => date('Y-m-d H:i:s'),
    //                   'status' => CONTENT_ACTIVE ));

    //     $cardPropertyList = $cardLib->getMasterCardPropertyList($defaultCard['master_card_id']);
    //     foreach($cardPropertyList as $cardProperty)
    //     {
    //       $cardPropertyValue = $cardLib->getCardPropertyValue($defaultCard['master_card_id'], DEFAULT_CARD_LEVEL_ID, $cardProperty['card_property_id']);
    //       $cardLib->insertUserCardProperty(array(
    //                       'user_id' => $userId,
    //                       'card_property_id' => $cardProperty['card_property_id'],
    //                       'user_card_id' => $userCardId,
    //                       'user_card_property_value' => $cardPropertyValue['card_property_value'],
    //                       'created_at' => date('Y-m-d H:i:s'),
    //                       'status' => CONTENT_ACTIVE));
    //     }
    //   }
    // }
  }

  public function deleteUser($userId, $options=array())
  {
    $sql = "DELETE FROM user
            WHERE user_id = :userId";

	  $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;
  }

  public function checkFacebookId($facebookId, $options=array())
  {
    $sql = "SELECT *
            FROM user
            WHERE facebook_id = '".$facebookId."'";

    $result = database::doSelectOne($sql);
    return $result;
  }

  public function getUserForGoogleId($accountId, $options = array())
  {
    $sql = "SELECT *
            FROM user
            WHERE google_id = :accountId";

    $result = database::doSelectOne($sql, array('accountId' => $accountId));
    return $result;
  }
  public function getUserForGameCenterId($gameCenterId, $options = array())
  {
    $sql = "SELECT *
            FROM user
            WHERE game_center_id = :gameCenterId";

    $result = database::doSelectOne($sql, array('gameCenterId' => $gameCenterId));
    return $result;
  }

  public function getMasterCubeRewardForStadium($cubeId, $stadiumId, $options = array())
  {
    $sql = "SELECT *
            FROM master_cube_reward
            WHERE cube_id = :cubeId AND master_stadium_id = :stadiumId";

    $result = database::doSelectOne($sql, array('cubeId' => $cubeId, 'stadiumId' => $stadiumId));
    return $result;
  }

  public function insertUserReward($options=array())
  {
    $sql = "INSERT INTO user_reward ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getUserRewardActiveList($userId, $rewardStatus, $options = array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE user_id = :userId AND status <>:rewardStatus";

    $result = database::doSelect($sql, array('userId' => $userId, 'rewardStatus' => $rewardStatus));
    return $result;
  }

  public function getUserRewardsActiveList($userId, $rewardStatus, $options = array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE user_id = :userId  AND cube_id IN (1,2,3,4,5) AND status <>:rewardStatus";

    $result = database::doSelect($sql, array('userId' => $userId, 'rewardStatus' => $rewardStatus));
    return $result;
  }
  public function getUserRewardActiveListForCube($userId, $cubeId, $rewardStatus, $options = array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE user_id = :userId  AND cube_id = :cubeId AND status <>:rewardStatus";

    $result = database::doSelect($sql, array('userId' => $userId, 'rewardStatus' => $rewardStatus, 'cubeId' => $cubeId));
    return $result;
  }

  public function getUserRewardDetail($userRewardId, $options = array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE user_reward_id = :userRewardId";

    $result = database::doSelectOne($sql, array('userRewardId' => $userRewardId));
    return $result;
  }

  public function updateUserReward($userRewardId, $options=array())
  {
    $sql = "UPDATE user_reward SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_reward_id =:userRewardId";
    $options['userRewardId'] = $userRewardId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function getProcessingUserReward($options = array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE status = :rewardStatus";

    $result = database::doSelect($sql, array('rewardStatus' => CUBE_ON_PROCESS ));
    return $result;
  }

  public function  checkForUserLevelUp($userId)
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $isLevelIncreased = false;

    $user = $userLib->getUserDetail($userId);
    $previousLevelUpXp = $cardLib->getMasterLevelUpXpForUserLevel($user['level_id']);
    if($user['xp'] >= $previousLevelUpXp['xp_to_next_level'])
    {
    //  $levelUpXp = $cardLib->getMasterLevelUpXpForUser($userId, $user['level_id']+1);
      $levelUpXp = $cardLib->getMasterLevelUpXpForUserLevel($user['level_id']+1);

      if(!empty($levelUpXp))
      {
        $userLib->updateUser($userId, array('level_id' => $levelUpXp['level_id'],
                                            'level_up' => 1,
                                            'xp'=>$user['xp'] - $previousLevelUpXp['xp_to_next_level'],
                                            // 'xp' => $levelUpXp['xp'] - $levelUpXp['xp_to_next_level'],
                                             'god_tower_health' => $levelUpXp['god_tower_health'],
                                             'stadium_tower_damage' => $levelUpXp['stadium_tower_damage'],
                                             'god_tower_damage' => $levelUpXp['god_tower_damage'],
                                             'stadium_tower_health' => $levelUpXp['stadium_tower_health']));
        $isLevelIncreased=true;
     }
   }

    return $isLevelIncreased;
  }
  public function  checkForUserLevelUpReady($userId)
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $isLevelIncreased = false;

    $user = $userLib->getUserDetail($userId);
    $previousLevelUpXp = $cardLib->getMasterLevelUpXpForUserLevel($user['level_id']);
    if($user['xp'] >= $previousLevelUpXp['xp_to_next_level'])
    {
    //  $levelUpXp = $cardLib->getMasterLevelUpXpForUser($userId, $user['level_id']+1);
      $levelUpXp = $cardLib->getMasterLevelUpXpForUserLevel($user['level_id']+1);

      if(!empty($levelUpXp))
      {
  /*      $userLib->updateUser($userId, array('level_id' => $levelUpXp['level_id'],
                                            'level_up' => 1,
                                            // 'xp' => $levelUpXp['xp'] - $levelUpXp['xp_to_next_level'],
                                             'god_tower_health' => $levelUpXp['god_tower_health'],
                                             'stadium_tower_damage' => $levelUpXp['stadium_tower_damage'],
                                             'god_tower_damage' => $levelUpXp['god_tower_damage'],
                                             'stadium_tower_health' => $levelUpXp['stadium_tower_health']));
*/
        $userLib->updateUser($userId, array('level_up' => 1));
        $isLevelIncreased = true;
      }
   }

    return $isLevelIncreased;
  }
  
  public function  getUserRewardForCanClaimStatusBasedOnCube($userId, $cubeId, $options=array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE user_id = :userId AND cube_id = :cubeId AND status = :rewardStatus
            ORDER BY created_at DESC ";

    $result = database::doSelect($sql, array('userId' => $userId, 'rewardStatus' => CUBE_CAN_BE_CLAIMED , 'cubeId' => $cubeId ));
    return $result;
  }

  public function getInactiveUserList($inActiveTime, $options=array())
  {
    $sql = "SELECT *
            FROM user
            WHERE notification_status = ".CONTENT_ACTIVE." AND last_access_time < '".(time()-$inActiveTime)."'
            ORDER BY RAND()";

    $result = database::doSelect($sql);
    return $result;
  }

  public function  getUserRewardCount($userId, $cubeId, $createdAt, $options=array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE user_id = :userId AND cube_id = :cubeId AND status = :rewardStatus
            AND created_at = :createdAt  ORDER BY created_at DESC";

    $result = database::doSelect($sql, array('userId' => $userId, 'rewardStatus' => CONTENT_CLOSED, 'cubeId' => $cubeId, 'createdAt' => $createdAt));
    return $result;
  }

  public function insertUserDailyAdReward($options=array())
  {
    $sql = "INSERT INTO user_daily_ad_reward ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getUserDailyAdReward($userId, $today, $options=array())
  {
    $sql = "SELECT *
            FROM user_daily_ad_reward
            WHERE user_id =:userId AND created_at = :createdAt";

    $result = database::doSelect($sql, array('userId' => $userId, 'createdAt' => $today));
    return $result;
  }
  public function getUserDailyAdRewardOne($userId, $today, $options=array())
  {
    $sql = "SELECT *
            FROM user_daily_ad_reward
            WHERE user_id =:userId AND created_at = :createdAt";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'createdAt' => $today));
    return $result;
  }
  /*function secure_random_string($length) {
    $random_string = '';
    for($i = 0; $i < $length; $i++) {
        $number = random_int(0, 36);
        $character = base_convert($number, 10, 36);
        $random_string .= $character;
    }
 
    return $random_string;
  }*/

  function secure_random_string($length = 9) {
    $pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, $length);
    return $pass;
}
 /* 
function secure_random_string($strength = 9) {
    $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
 
    return $random_string;
}*/
  public function getKingdomLoginAccess($userId, $options=array())
  {
    $sql = "SELECT *
            FROM kingdom_last_access
            WHERE user_id =:userId";

    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result;
  }
  public function insertKingdomLoginAccess($userId, $options=array())
  {
    $sql = "INSERT INTO kingdom_last_access ";
    $sql .= "( ".implode(", ", array_keys($options))." ) ";
    $sql .= "SELECT * FROM (SELECT :".implode(", :", array_keys($options))." ) AS tmp
    WHERE NOT EXISTS (
        SELECT user_id FROM kingdom_last_access WHERE user_id = ".$userId."
    ) LIMIT 1";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function updateKingdomLoginAccess($userId, $options=array())
  {
    $sql = "UPDATE kingdom_last_access SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_id =:userId";
    $options['userId'] = $userId;

    $result = database::doUpdate($sql, $options);

    return $result;
  }
  public function deleteKingdomLoginAccess($options=array())
  {
    $sql = "DELETE FROM kingdom_messages km
            WHERE EXISTS (SELECT  * 
            FROM kingdom_last_access kla
            WHERE kla.last_access < (UNIX_TIMESTAMP() - 5) AND km.battle_state=1 AND kla.user_id = km.sent_by)";

	  $result = database::doDelete($sql, $options);
    return $result;
  }
  public function updateToCancelKingdomChatWithLoginAccess($options=array())
  {
    $sql = "UPDATE kingdom_messages km
            SET battle_state=4
            WHERE EXISTS (SELECT  * 
            FROM kingdom_last_access kla
            WHERE kla.last_access < (UNIX_TIMESTAMP() - 5) AND km.battle_state=1 AND km.msg_type=3 AND kla.user_id = km.sent_by)";

	  $result = database::doDelete($sql, $options);
    return $result;
  }
  public function getListToCancelKingdomChatWithLoginAccess($options=array())
  {
    $sql = "SELECT *
            FROM kingdom_messages km
            WHERE EXISTS (SELECT  * 
            FROM kingdom_last_access kla
            WHERE kla.last_access < (UNIX_TIMESTAMP() - 5) AND km.battle_state=1 AND km.msg_type=3 AND kla.user_id = km.sent_by)";

	  $result = database::doSelect($sql, $options);
    return $result;
  }
}
