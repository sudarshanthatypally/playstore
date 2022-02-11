<?php
class badge{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getMasterBadgeList($options=array())
  {
    $sql = "SELECT *
            FROM master_badge";

    $result = database::doSelect($sql);
    return $result;
  }
  public function getMasterBadgeMinimumList($options=array())
  {
    $sql = "SELECT *
            FROM master_badge";

    $result = database::doSelectOne($sql);
    return $result;
  }

  public function getMasterBadgeDetail($masterBadgeId, $options=array())
  {
    $sql = "SELECT *
            FROM master_badge
            WHERE master_badge_id=:masterBadgeId";

    $result = database::doSelectOne($sql, array('masterBadgeId'=>$masterBadgeId));
    return $result;
  }

  public function insertMasterBadge($options=array())
  {
    $sql = "INSERT INTO master_badge ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function updateMasterBadge($masterBadgeId, $options=array())
  {
    $sql = "UPDATE master_badge SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE master_badge_id =:masterBadgeId";
    $options['masterBadgeId'] = $masterBadgeId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function deleteMasterBadge($masterBadgeId, $options=array())
  {
    $sql = "DELETE FROM master_badge
            WHERE master_badge_id = :masterBadgeId";

	  $result = database::doDelete($sql, array('masterBadgeId'=>$masterBadgeId));
    return $result;
  }

  public function getUserBadgeListForBadgeId($userId, $masterBadgeId, $options=array())
  {
    $sql = "SELECT *
            FROM user_badge
            WHERE user_id = :userId And master_badge_id = :masterBadgeId";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'masterBadgeId' => $masterBadgeId));
    return $result;
  }
  public function getUserRelicsDiff($relics, $options=array())
  {
    $sql = " SELECT * 
            FROM master_badge 
            WHERE min_relic_count<=:relics
            ORDER BY min_relic_count DESC
            LIMIT 1";

    $result = database::doSelectOne($sql, array('relics' => $relics));
    return $result;
  }
  public function getCurrentSeasonLeague($options=array())
  {
    date_default_timezone_set('Asia/Kolkata');
    $timestamp = date("Y-m-d H:i:s");
    $sql = "SELECT *
            FROM master_season_league
            WHERE status=1 AND start_time < '".$timestamp."' AND expiry_date > '".$timestamp."'
            ORDER BY start_time DESC 
            LIMIT 1";

    $result = database::doSelect($sql, $options);
    return $result;
  }
  public function insertSeasonLeague($options=array())
  {
    $sql = "INSERT INTO master_season_league ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function insertUserBadge($options=array())
  {
    $sql = "INSERT INTO user_badge ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getBadgeForRelics($relics, $options=array())
  {
    $sql = "SELECT *
            FROM master_badge
            WHERE min_relic_count <= ".$relics." AND max_relic_count >= ".$relics;

    $result = database::doSelectOne($sql, array('relics' => $relics));
    return $result;
  }

  public function checkUserBadge($userId)
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $user = $userLib->getUserDetail($userId);
    $badgeAchieved = new ArrayObject();

    $relics = $user['relics'];
    $badge = $this->getBadgeForRelics($relics);
    if(!empty($badge))
    {
      $userBadge = $this->getUserBadgeListForBadgeId($userId, $badge['master_badge_id']);
      if(empty($userBadge))
      {
        $this->insertUserBadge(array(
          'user_id' => $userId,
          'master_badge_id' => $badge['master_badge_id'],
          'created_at' => date('Y-m-d H:i:s'),
          'status' => CONTENT_ACTIVE
        ));
        $badgeAchieved['master_badge_id'] = $badge['master_badge_id'];
      }
    }

    return $badgeAchieved;

  }

  public function getUserLatestBadge($userId, $options=array())
  {
    $sql = "SELECT MAX(master_badge_id) as master_badge_id
            FROM user_badge
            WHERE user_id = :userId";

    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result;
  }
}
