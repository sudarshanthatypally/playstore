<?php
class quest{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getMasterQuestDetail($options=array())
  {
    $sql = "SELECT *
            FROM master_quest
            WHERE status=1
            ORDER BY (case when frequency = '2' then 1
               when frequency = '3' then 2
                      when frequency = '4' then 3
                      when frequency = '1' then 4
                      when frequency = '0' then 5
               else 3
          end),
         frequency";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getMasterCardListWithStadium($options=array())
  {
    $sql = "SELECT mc.master_card_id, mc.title,mc.card_max_level,mc.card_rarity_type,mc.is_available,mc.card_description,mc.bundlename,mc.android_bundlehash,mc.android_version_id, mc.ios_version_id,mc.android_bundlecrc,mc.ios_bundlehash,mc.ios_bundlecrc, mc.card_type, mc.is_card_default,ms.title as stadium_title, ms.master_stadium_id
            FROM master_card mc
            LEFT JOIN master_stadium ms ON mc.master_stadium_id = ms.master_stadium_id
            ORDER BY mc.master_card_id";
 
    $result = database::doSelect($sql);
    return $result;
  }

    public function getCardPrevious($userId){
    $sql = "SELECT uc.*
            FROM user_card uc
            WHERE uc.user_id=:userId";
    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }

  public function getPercentageofCardwithMasterId($masterId){
    $sql = "SELECT master_card_id,probability
            FROM master_card_probability 
            WHERE master_stadium_id=:masterId AND probability <> 0";
    $result = database::doSelect($sql, array('masterId'=>$masterId));
    return $result;
  }

  public function getinventoryForQuestOne($userId){
    $sql = "SELECT * 
            FROM quest_inventory 
            WHERE quest_id=1 AND user_id=:userId AND TIME > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = database::doSelect($sql, array('userId'=>$userId));
    return $result;
  }
  
  
  public function getMasterCardDetail($cardId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE master_card_id = :cardId";

    $result = database::doSelectOne($sql, array('cardId' => $cardId));
    return $result;
  }

  public function insertMasterQuestInventory($options=array())
  {
    $sql = "INSERT INTO quest_inventory ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function insertMasterQuestClaimed($options=array())
  {
    $sql = "INSERT INTO quest_claim ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function updateQuestInventory($questId, $userId, $options=array())
  {
    $sql = "UPDATE quest_inventory SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE quest_id =:questId AND user_id =:userId";
    $options['questId'] = $questId;
    $options['userId'] = $userId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function getQuestDetails($questId){
    $sql = "SELECT * 
            FROM master_quest 
            WHERE master_quest_id=:questId";
    $result = database::doSelectOne($sql, array('questId'=>$questId));
    return $result;
  }
  public function getBattleQuestDetails($questId, $userId){
    $sql = "SELECT * 
            FROM master_quest 
            WHERE master_quest_id<=:questId";
            //AND user_id=:userId  'userId'=>$userId,
    $result = database::doSelectOne($sql, array('questId'=>$questId));
    return $result;
  }
  public function getBattleQuestData($questId, $userId){
    $sql = "SELECT *     
            FROM quest_inventory qi
            WHERE qi.quest_id=:questId AND qi.user_id=:userId ";
    $result = database::doSelectOne($sql, array('questId'=>$questId, 'userId'=>$userId));
    return $result;
  }
  public function getQuestCollectFreeReward($userId, $andVerId, $iosVerId){
    date_default_timezone_set('Asia/Kolkata');
    
    if(!empty($andVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId 
        AND qi.time >= CURDATE() AND qi.time < CURDATE() + INTERVAL 1 DAY
        AND INET_ATON(mq.android_version_id)<=INET_ATON('".$andVerId."') 
        ORDER BY qi.quest_inventory_id DESC"; //> NOW() - INTERVAL 1 DAY
    }elseif(!empty($iosVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId
        AND qi.time >= CURDATE() AND qi.time < CURDATE() + INTERVAL 1 DAY
        AND INET_ATON(mq.ios_version_id)<=INET_ATON('".$iosVerId."') 
      ORDER BY qi.quest_inventory_id DESC";
    }else{
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId
        AND qi.time >= CURDATE() AND qi.time < CURDATE() + INTERVAL 1 DAY
      ORDER BY qi.quest_inventory_id DESC";
    }
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  /*public function getQuestPatBattle100Reward($userId, $andVerId, $iosVerId){
    if(!empty($andVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=2 
        AND qi.time > NOW() - INTERVAL 7 DAY 
        AND INET_ATON(mq.android_version_id)<=INET_ATON('".$andVerId."') 
        ORDER BY qi.quest_inventory_id DESC";
    }elseif(!empty($iosVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=2 
        AND qi.time > NOW() - INTERVAL 7 DAY 
        AND INET_ATON(mq.ios_version_id)<=INET_ATON('".$iosVerId."') 
      ORDER BY qi.quest_inventory_id DESC";
    }else{
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=2
        AND qi.time > NOW() - INTERVAL 7 DAY
      ORDER BY qi.quest_inventory_id DESC";
    }
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }*/
  public function getQuestPatBattle100Reward($userId, $andVerId, $iosVerId){
    if(!empty($andVerId) || !empty($iosVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=2
      AND qi.time BETWEEN (DATE_ADD(CURDATE(), INTERVAL - WEEKDAY(CURDATE()) DAY)) AND (DATE(ADDDATE(NOW(), 2 - DAYOFWEEK(NOW())+ CASE WHEN DAYOFWEEK(NOW()) < 2 THEN 0 ELSE 7 END )))
        AND (INET_ATON(mq.android_version_id)<=INET_ATON('".$andVerId."') OR INET_ATON(mq.ios_version_id)<=INET_ATON('".$iosVerId."'))
        ORDER BY qi.quest_inventory_id DESC";//        AND qi.time > NOW() - INTERVAL 7 DAY 
    }else{
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=2
      AND qi.time BETWEEN (DATE_ADD(CURDATE(), INTERVAL - WEEKDAY(CURDATE()) DAY)) AND (DATE(ADDDATE(NOW(), 2 - DAYOFWEEK(NOW())+ CASE WHEN DAYOFWEEK(NOW()) < 2 THEN 0 ELSE 7 END )))
      ORDER BY qi.quest_inventory_id DESC"; //        AND qi.time > NOW() - INTERVAL 7 DAY
    }
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getQuestPlayBattle200Reward($userId, $andVerId, $iosVerId){
    if(!empty($andVerId) || !empty($iosVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=10
        AND (INET_ATON(mq.android_version_id)<=INET_ATON('".$andVerId."') OR INET_ATON(mq.ios_version_id)<=INET_ATON('".$iosVerId."'))
        ORDER BY qi.quest_inventory_id DESC";//        AND qi.time > NOW() - INTERVAL 7 DAY 
    }else{
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=10
      ORDER BY qi.quest_inventory_id DESC"; //        AND qi.time > NOW() - INTERVAL 7 DAY
    }
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getQuestPlayBattle500Reward($userId, $andVerId, $iosVerId){
    if(!empty($andVerId) || !empty($iosVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=11
        AND (INET_ATON(mq.android_version_id)<=INET_ATON('".$andVerId."') OR INET_ATON(mq.ios_version_id)<=INET_ATON('".$iosVerId."'))
        ORDER BY qi.quest_inventory_id DESC";//AND qi.time > NOW() - INTERVAL 7 DAY 
    }else{
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=11
      ORDER BY qi.quest_inventory_id DESC"; // AND qi.time > NOW() - INTERVAL 7 DAY
    }
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getQuestKathikaReward($userId, $andVerId, $iosVerId){
    if(!empty($andVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=3
        AND INET_ATON(mq.android_version_id)<=INET_ATON('".$andVerId."') 
        ORDER BY qi.quest_inventory_id DESC";//AND qi.time > NOW() - INTERVAL 1 MONTH
    }elseif(!empty($iosVerId)){
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=3
        AND INET_ATON(mq.ios_version_id)<=INET_ATON('".$iosVerId."') 
      ORDER BY qi.quest_inventory_id DESC"; //AND qi.time > NOW() - INTERVAL 1 MONTH
    }else{
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=3
      ORDER BY qi.quest_inventory_id DESC"; //AND qi.time > NOW() - INTERVAL 1 MONTH
    }
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getQuestKingdomReward($userId){
    
      $sql = "SELECT * 
      FROM quest_inventory qi
      LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
      WHERE user_id=:userId AND quest_id=5
      ORDER BY qi.quest_inventory_id DESC"; //AND qi.time > NOW() - INTERVAL 1 MONTH
    
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
 /* public function getQuestUserStadium5Reward($userId){
    
    $sql = "SELECT * 
    FROM quest_inventory qi
    LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
    WHERE user_id=:userId AND quest_id=7
    ORDER BY qi.quest_inventory_id DESC"; //AND qi.time > NOW() - INTERVAL 1 MONTH
  
  $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
  return $result;
}*/
public function getQuestUserStadium5Reward($userId){
    $sql = "SELECT * 
    FROM quest_inventory qi
    LEFT JOIN master_quest AS mq ON mq.master_quest_id=qi.quest_id
    WHERE user_id=:userId AND quest_id=7
    ORDER BY qi.quest_inventory_id DESC"; 
  
  $result = database::doSelectOne($sql, array('userId'=>$userId));
  return $result;
}
  public function getQuestCollectFreeRewardClaimed($userId){
      $sql = "SELECT * 
      FROM quest_claim qc
      WHERE user_id=:userId AND quest_id=1
        AND qc.created_at >= CURDATE() AND qc.created_at < CURDATE() + INTERVAL 1 DAY
      ORDER BY qc.quest_claim_id DESC"; //> NOW() - INTERVAL 1 DAY
    
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getQuestKathikaRewardInKathika($userId){ 
    $sql = "SELECT * 
    FROM kathika_property
    WHERE user_id=:userId 
    ORDER BY kathika_prop_id DESC";//AND created_at > NOW() - INTERVAL 1 MONTH
    
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  } 
  public function getPlayBattle100QuestClaimed($userId){
    $sql = "SELECT * 
    FROM quest_claim qc
    WHERE user_id=:userId AND quest_id=2
    AND qc.created_at BETWEEN (DATE_ADD(CURDATE(), INTERVAL - WEEKDAY(CURDATE()) DAY)) AND (DATE(ADDDATE(NOW(), 2 - DAYOFWEEK(NOW())+ CASE WHEN DAYOFWEEK(NOW()) < 2 THEN 0 ELSE 7 END )))
    ORDER BY qc.quest_claim_id DESC";
  
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId //AND qc.created_at > NOW() - INTERVAL 1 DAY
    return $result;
  }
  public function getPlayBattle200QuestClaimed($userId){
    $sql = "SELECT * 
    FROM quest_claim qc
    WHERE user_id=:userId AND quest_id=10
    ORDER BY qc.quest_claim_id DESC"; //      AND qc.created_at > NOW() - INTERVAL 7 DAY

    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getPlayBattle500QuestClaimed($userId){
    $sql = "SELECT * 
    FROM quest_claim qc
    WHERE user_id=:userId AND quest_id=11
    ORDER BY qc.quest_claim_id DESC"; //      AND qc.created_at > NOW() - INTERVAL 7 DAY
  
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getKathikaQuestClaimed($userId){
    $sql = "SELECT * 
    FROM quest_claim qc
    WHERE user_id=:userId AND quest_id=3
    ORDER BY qc.quest_claim_id DESC";//AND qc.created_at > NOW() - INTERVAL 1 MONTH
  
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getKingdomQuestClaimed($userId){
    $sql = "SELECT * 
    FROM quest_claim qc
    WHERE user_id=:userId AND quest_id=5
    ORDER BY qc.quest_claim_id DESC";//AND qc.created_at > NOW() - INTERVAL 1 MONTH
  
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
  public function getQuestUserStadium5Claimed($userId){
    $sql = "SELECT * 
    FROM quest_claim qc
    WHERE user_id=:userId AND quest_id=7
    ORDER BY qc.quest_claim_id DESC";//AND qc.created_at > NOW() - INTERVAL 1 MONTH
  
    $result = database::doSelectOne($sql, array('userId'=>$userId));//,'andVerId'=>$andVerId, 'iosVerId'=>$iosVerId
    return $result;
  }
}
