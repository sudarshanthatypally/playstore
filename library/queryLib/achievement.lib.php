<?php
class achievement{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getMasterAchievementList($options=array())
  {
    $sql = "SELECT *
            FROM master_achievement";

    $result = database::doSelect($sql);
    return $result;
  }

  public function insertUserAchievement($options=array())
  {
    $sql = "INSERT INTO user_achievement ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getUserAchievmentDetail($userId, $masterAchievementId, $options=array())
  {
    $sql = "SELECT *
            FROM user_achievement
            WHERE master_achievement_id = :masterAchievementId AND user_id = :userId";

    $result = database::doSelect($sql, array('userId' => $userId, 'masterAchievementId' => $masterAchievementId));
    return $result;
  }

  public function getUserAchievementList($userId, $options=array())
  {
    $sql = "SELECT  master_achievement.*
            FROM user_achievement
            INNER JOIN master_achievement ON master_achievement.master_achievement_id = user_achievement.master_achievement_id
            WHERE user_achievement.user_id = :userId";

    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }

  public function getUserAchievementListForAchievementId($userId, $masterAchievementId, $options=array())
  {
    $sql = "SELECT *
            FROM user_achievement
            WHERE user_id = :userId And master_achievement_id = :masterAchievementId";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'masterAchievementId' => $masterAchievementId));
    return $result;
  }

  public function checkUserAchievement($userId)
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');

    $achieved = array();
    $user = $userLib->getUserDetail($userId);

    //Get the achievement list for type = totalWin.
    $masterAchievementList = $achievementLib->getMasterAchievementListForType(ACHIEVEMENT_TYPE_TOTAL_WIN);

    foreach ($masterAchievementList as $achievement)
    {
      $userAchivement = $achievementLib->getUserAchievementListForAchievementId($userId, $achievement['master_achievement_id']);
      $totalWin = $roomLib->getTotalWinStatusToUser($userId, BATTLE_WON_STATUS);
      if($totalWin['win_count'] == $achievement['count'] && empty($userAchivement))
      {
        $achievementLib->insertUserAchievement(array('user_id' => $userId,
                           'master_achievement_id' => $achievement['master_achievement_id'],
                           'created_at' => date('Y-m-d H:i:s'),
                           'status' => CONTENT_ACTIVE));

        $userLib->updateUser($userId, array('xp' => $user['xp'] + $achievement['xp']));
        $achieved[]['master_achievement_id'] = $achievement['master_achievement_id'];
      }
      $user = $userLib->getUserDetail($userId);
    }

    $user = $userLib->getUserDetail($userId);

    //Check whether user entered new stadium .
    $masterAchievementList = $achievementLib->getMasterAchievementListForType(ACHIEVEMENT_TYPE_STADIUM);

    foreach ($masterAchievementList as $achievement)
    {
      $userAchivement = $achievementLib->getUserAchievementListForAchievementId($userId, $achievement['master_achievement_id']);

      if($user['master_stadium_id'] == $achievement['count'] && empty($userAchivement))
      {
        $achievementLib->insertUserAchievement(array('user_id' => $userId,
                           'master_achievement_id' => $achievement['master_achievement_id'],
                           'created_at' => date('Y-m-d H:i:s'),
                           'status' => CONTENT_ACTIVE));

        $userLib->updateUser($userId, array('xp' => $user['xp']+$achievement['xp']));
        $achieved[]['master_achievement_id'] = $achievement['master_achievement_id'];
      }
      $user = $userLib->getUserDetail($userId);
    }


    //Continus win.
    $masterAchievementList = $achievementLib->getMasterAchievementListForType(ACHIEVEMENT_TYPE_CONTINUOUS_WIN);
    $user = $userLib->getUserDetail($userId);

    foreach ($masterAchievementList as $achievement)
    {
      $userMatchList = $roomLib->getWaitingRoomContinuesWinCount($userId);
      $winCount = 0;

      foreach ($userMatchList as $userMatch)
      {
        if($userMatch['win_status'] == BATTLE_WON_STATUS){
          $winCount++;
        } else {
          break;
        }
      }

      if($winCount == $achievement['count'])
      {
        $userAchivement = $achievementLib->getUserAchievementListForAchievementId($userId, $achievement['master_achievement_id']);
        if(empty($userAchivement))
        {
          $achievementLib->insertUserAchievement(array('user_id' => $userId,
                             'master_achievement_id' => $achievement['master_achievement_id'],
                             'created_at' => date('Y-m-d H:i:s'),
                             'status' => CONTENT_ACTIVE));

          $userLib->updateUser($userId, array('xp' => $user['xp'] + $achievement['xp']));
          $achieved[]['master_achievement_id'] = $achievement['master_achievement_id'];
        }
        $user = $userLib->getUserDetail($userId);
      }
    }

    $user = $userLib->getUserDetail($userId);

    //Achievement based on collecting 1000 relic.
    $masterAchievementList = $achievementLib->getMasterAchievementListForType(ACHIEVEMENT_TYPE_RELIC_COUNT);

    foreach ($masterAchievementList as $achievement)
    {
      $userAchivement = $achievementLib->getUserAchievementListForAchievementId($userId, $achievement['master_achievement_id']);

      if($user['relics'] >= $achievement['count'] && empty($userAchivement))
      {
        $achievementLib->insertUserAchievement(array('user_id' => $userId,
                           'master_achievement_id' => $achievement['master_achievement_id'],
                           'created_at' => date('Y-m-d H:i:s'),
                           'status' => CONTENT_ACTIVE));

        $userLib->updateUser($userId, array('xp' => $user['xp'] + $achievement['xp']));
        $achieved[]['master_achievement_id'] = $achievement['master_achievement_id'];
      }
      $user = $userLib->getUserDetail($userId);
    }

    return $achieved;
  }

  public function checkCardUnlockAchivement($userId)
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    $achieved = array();

    //50 % card unlocked or not
    $achievement = $achievementLib->getMasterAchievementDetailForType(ACHIEVEMENT_TYPE_CARD_UNLOCK);
    $userAchivement = $achievementLib->getUserAchievementListForAchievementId($userId, $achievement['master_achievement_id']);
    $user = $userLib->getUserDetail($userId);

    //Get List of Cards which user has.
    $userCardList = $cardLib->getUserCardListForUserId($userId);
    $masterCardList = $cardLib->getMasterCardList();
    if((round((count($userCardList)/count($masterCardList))*100) >= 50) && empty($userAchivement))
    {
      $achievementLib->insertUserAchievement(array('user_id' => $userId,
                         'master_achievement_id' => $achievement['master_achievement_id'],
                         'created_at' => date('Y-m-d H:i:s'),
                         'status' => CONTENT_ACTIVE));

      $achieved[]['master_achievement_id'] = $achievement['master_achievement_id'];
      $userLib->updateUser($userId, array('xp' => $user['xp'] + $achievement['xp']));
    }

    //all card unlocked
    $achievement = $achievementLib->getMasterAchievementDetailForType(ACHIEVEMENT_TYPE_ALL_CARD_UNLOCK);
    $userAchivement = $achievementLib->getUserAchievementListForAchievementId($userId, $achievement['master_achievement_id']);
    $user = $userLib->getUserDetail($userId);

    if((round((count($userCardList)/count($masterCardList))*100) == 100) && empty($userAchivement))
    {
      $achievementLib->insertUserAchievement(array('user_id' => $userId,
                         'master_achievement_id' => $achievement['master_achievement_id'],
                         'created_at' => date('Y-m-d H:i:s'),
                         'status' => CONTENT_ACTIVE));

      $achieved[]['master_achievement_id'] = $achievement['master_achievement_id'];
      $userLib->updateUser($userId, array('xp' => $user['xp'] + $achievement['xp']));
    }
    return $achieved;
  }

  public function checkCubeOpenedAchivement($userId)
  {
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    $achieved = array();

    //Achievement based on openning cube count.
    $masterAchievementList = $achievementLib->getMasterAchievementListForType(ACHIEVEMENT_TYPE_CUBE);
    $user = $userLib->getUserDetail($userId);

    foreach ($masterAchievementList as $achievement)
    {
      $cubeOpenedCount = $rewardLib->getUserRewardListForDate($userId, $achievement['cube_id']);
      $userAchivement = $achievementLib->getUserAchievementListForAchievementId($userId, $achievement['master_achievement_id']);
      if(count($cubeOpenedCount) >= $achievement['count'] && empty($userAchivement))
      {
        $achievementLib->insertUserAchievement(array('user_id' => $userId,
                           'master_achievement_id' => $achievement['master_achievement_id'],
                           'created_at' => date('Y-m-d H:i:s'),
                           'status' => CONTENT_ACTIVE));

        $userLib->updateUser($userId, array('xp' => $user['xp'] + $achievement['xp']));
        $achieved[]['master_achievement_id'] = $achievement['master_achievement_id'];
        $user = $userLib->getUserDetail($userId);
      }
    }
    return $achieved;
  }

  public function deleteMasterAchievement($masterAchievementId, $options=array())
  {
    $sql = "DELETE FROM master_achievement
            WHERE master_achievement_id = :masterAchievementId";

	  $result = database::doDelete($sql, array('masterAchievementId' => $masterAchievementId));
    return $result;
  }

  public function getUserAchievementRecentDetail($userId, $masterAchievementId, $options=array())
  {
    $sql = "SELECT  *
            FROM user_achievement
            WHERE user_achievement.user_id = :userId AND user_achievement.master_achievement_id = :masterAchievementId
            ORDER BY created_at DESC";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'masterAchievementId' => $masterAchievementId));
    return $result;
  }

  public function getMasterAchievementListForType($type, $options=array())
  {
    $sql = "SELECT *
            FROM master_achievement
            WHERE master_achievement.achievement_type = :type";

    $result = database::doSelect($sql, array('type' => $type));
    return $result;
  }

  public function getMasterAchievementDetail($masterAchievementId, $options=array())
  {
    $sql = "SELECT *
            FROM master_achievement
            WHERE master_achievement_id = :masterAchievementId";

    $result = database::doSelectOne($sql, array('masterAchievementId' => $masterAchievementId));
    return $result;
  }

  public function updateMasterAchievement($masterAchievemntId, $options=array())
  {
    $sql = "UPDATE master_achievement SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE master_achievement_id = :masterAchievementId";
    $options['masterAchievementId'] = $masterAchievemntId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function insertMasterAchievement($options=array())
  {
    $sql = "INSERT INTO master_achievement";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getMasterAchievementDetailForType($type, $options=array())
  {
    $sql = "SELECT *
            FROM master_achievement
            WHERE master_achievement.achievement_type = :type";

    $result = database::doSelectOne($sql, array('type' => $type));
    return $result;
  }

}
