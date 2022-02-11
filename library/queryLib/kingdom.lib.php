<?php
class kingdom{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      date_default_timezone_set('Asia/Kolkata');
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getKingdomList($options=array())
  {
    $sql = "SELECT *
            FROM kingdom";

    $result = database::doSelect($sql);
    return $result;
  }
  public function getKingdomListWithRank($options=array())
  {
    /*$sql = "SELECT k.*, ku.user_trophies, ROW_NUMBER() OVER (ORDER BY ku.user_trophies DESC) AS srno
            FROM kingdom k
            LEFT JOIN kingdom_users ku ON ku.kingdom_id=k.kingdom_id";*/
    $sql = "SELECT k.*, AVG(u.relics) AS user_trophy, ROW_NUMBER() OVER (ORDER BY AVG(u.relics) DESC) AS srno
            FROM kingdom k
            LEFT JOIN user u ON u.kingdom_id=k.kingdom_id
            GROUP BY k.kingdom_id";
    $result = database::doSelect($sql);
    return $result;
  }
  public function getKingdomRankInventory($userCnt, $options=array())
  {
    $sql = "SELECT *
            FROM kingdom_ranking_inventory as kri
            WHERE min<=:userCnt
            ORDER BY kingdom_ranking_id DESC";
    $result = database::doSelectOne($sql,array('userCnt' => $userCnt));
    return $result;
  }
  public function getKingdomRankRelicsTotal($kingdomId, $options=array())
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelics($kingdomId);
    $uCount=1;
    $user1to10 = $user11to20 = $user21to30 = $user31to40 = $user41to50 =0;
    foreach($kingdomDetailsOnRelics as $ku){
      $userDetails = $userLib->getUserDetail($ku['user_id']);
      $tempUsers = array();
      $tempUsers['rank'] = $ku['srno'];
      $userList[] = $tempUsers;
      $kingdomRankUser = $kingdomLib->getKingdomRankInventory($tempUsers['rank']);
      if($uCount<=10){
        $user1to10+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      if($uCount>=11 && $uCount<=20){
        $user11to20+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      if($uCount>=21 && $uCount<=30){
        $user21to30+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      if($uCount>=31 && $uCount<=40){
        $user31to40+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      if($uCount>=41 && $uCount<=50){
        $user41to50+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      $uCount++;
    }
    $totalRelics= $user1to10 + $user11to20 + $user21to30+ $user31to40 +$user41to50;
    return $totalRelics;
  }

  public function insertKingdom($options=array())
  {
    $sql = "INSERT INTO kingdom ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function insertKingdomMsg($options=array())
  {
    $sql = "INSERT INTO kingdom_messages ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function insertKingdomCardRequest($options=array())
  {
    $sql = "INSERT INTO card_request_inventory ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function getCardRequestDetail($userId, $options=array())
  {
    $sql = "SELECT *
            FROM kingdom_messages as km
            INNER JOIN card_request_inventory as cri ON cri.msg_id=km.km_id
            WHERE cri.user_id = :userId";

    $result = database::doSelectOne($sql, array('userId'=>$userId));
    return $result;
  }
  public function getRequestedOfCardRequestDetail($userId,$requestType, $currentTime, $msgId, $options=array())
  {
    if(empty($msgId)){
      $sql = "SELECT *
            FROM card_request_inventory
            WHERE user_id=:userId AND request_type=:requestType AND end_time>='".$currentTime."'
            ORDER BY card_request_inventory_id ASC";
    }else{
      $sql = "SELECT *
              FROM card_request_inventory
              WHERE user_id=:userId AND request_type=:requestType AND end_time>='".$currentTime."' AND msg_id=".$msgId."
              ORDER BY card_request_inventory_id ASC";
    }
    $result = database::doSelectOne($sql, array('userId'=>$userId,'requestType'=>$requestType));
    return $result;
  }
  
  public function getKingdomMsgList($kingdomId, $lastMsgId, $options=array())
  {
    if($lastMsgId==0 || empty($lastMsgId)){
     /* $sql = "SELECT *
            FROM kingdom_messages
            WHERE kingdom_id = :kingdomId";*/
            
      $sql = "SELECT *
      FROM (SELECT *
            FROM kingdom_messages
            WHERE kingdom_id = :kingdomId AND battle_state!=4
            HAVING room_id IS NULL
            UNION
              SELECT *
              FROM kingdom_messages
              WHERE kingdom_id = :kingdomId
              GROUP BY room_id
              HAVING room_id IS NOT NULL
              ORDER BY km_id DESC
              LIMIT 100) lst
                  ORDER BY km_id ASC";
    }else{ 
     /* $sql = "SELECT *
            FROM kingdom_messages
            WHERE kingdom_id = :kingdomId AND km_id > $lastMsgId";*/
      $sql = "SELECT *
              FROM (SELECT *
                FROM kingdom_messages
                WHERE kingdom_id = :kingdomId AND km_id > $lastMsgId
                HAVING room_id IS NULL
                UNION
                  SELECT *
                  FROM kingdom_messages
                  WHERE kingdom_id = :kingdomId AND km_id > $lastMsgId
                  GROUP BY room_id
                  HAVING room_id IS NOT NULL
                  ORDER BY km_id DESC
                  LIMIT 100) lst
                  ORDER BY km_id ASC";
            //battle_state!=4 AND 
    }
    //UNION ALL (SELECT * FROM kingdom_messages WHERE is_update=1 ORDER BY updated_at DESC LIMIT 1)
    $result = database::doSelect($sql, array('kingdomId' => $kingdomId));
    return $result;
  }
  public function insertKingdomUser($options=array())
  {
    $sql = "INSERT INTO kingdom_users ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  } 

  public function updateKingdom($kingdomId, $options=array())
  {
    $sql = "UPDATE kingdom SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE kingdom_id =:kingdomId";
    $options['kingdomId'] = $kingdomId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function updateKingdomUser($userId,$kingdomId, $options=array())
  {
    $sql = "UPDATE kingdom_users SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_id =:userId AND kingdom_id=:kingdomId";
    $options['userId'] = $userId;
    $options['kingdomId'] = $kingdomId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function updateKingdomMessage($userId,$kingdomId, $options=array())
  {
    $sql = "UPDATE kingdom_messages SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_id =:userId AND kingdom_id=:kingdomId";
    $options['userId'] = $userId;
    $options['kingdomId'] = $kingdomId;

    $result = database::doUpdate($sql, $options);
    return $result; 
  }
  public function updateKingdomReqMessage($msgId, $options=array())
  {
    $sql = "UPDATE kingdom_messages SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", "); 
    $sql .= " WHERE km_id=:msgId";
    $options['msgId'] = $msgId;
    //$options['kingdomId'] = $kingdomId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function updateCardReqMessage($cardRequestInventoryId, $options=array())
  {
    $sql = "UPDATE card_request_inventory SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", "); 
    $sql .= " WHERE card_request_inventory_id=:cardRequestInventoryId"; //msg_id=:msgId AND 
    //$options['msgId'] = $msgId;
    $options['cardRequestInventoryId'] = $cardRequestInventoryId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function updateCardReqMessageLastId($msgId, $options=array())
  {
    $sql = "UPDATE card_request_inventory SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", "); 
    $sql .= " WHERE msg_id=:msgId";
    $options['msgId'] = $msgId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function getKingdomUserDetail($userId, $options=array())
  {
    $sql = "SELECT *
            FROM kingdom_users
            WHERE user_id = :userId";

    $result = database::doSelectOne($sql, array('userId'=>$userId));
    return $result;
  }
  public function getUserCardDetail($userId, $masterCardId, $options=array())
  {
    $sql = "SELECT *
            FROM user_card as uc
            WHERE uc.user_id = :userId AND uc.master_card_id=:masterCardId";

    $result = database::doSelectOne($sql, array('userId'=>$userId, 'masterCardId'=> $masterCardId)); 
    return $result;
  }
  public function updateUserCard($userId, $masterCardId, $options=array())
  {
    $sql = "UPDATE user_card SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_id =:userId ";
    $sql .= "AND master_card_id=:masterCardId";
    $options['userId'] = $userId;
    $options['masterCardId'] = $masterCardId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function deleteKingdomRequestedMsg($userId, $kingdomId=0, $msgType, $options=array())
  { 
    if($msgType==7){
      $sql = "DELETE FROM kingdom_messages
              WHERE kingdom_id =".$kingdomId." AND msg_type =".$msgType." AND sent_by = :userId";
    }else{
      $sql = "DELETE FROM kingdom_messages
              WHERE sent_by = :userId AND kingdom_id = ".$kingdomId;
    }
    $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;
  }
  public function deleteKingdomRequestedMsgList($userId,  $msgType, $options=array())
  { 
    if($msgType==7){
      $sql = "DELETE FROM kingdom_messages
              WHERE msg_type =".$msgType." AND sent_by = :userId";
    }else if($msgType==3){
      $sql = "DELETE FROM kingdom_messages
              WHERE msg_type =".$msgType." AND battle_state=1 AND sent_by = :userId";
    }
    $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;  
  }
  public function deleteKingdomRequestedMsgListMsgType($userId, $msgType, $battleState, $options=array())
  { 
  
    $sql = "DELETE FROM kingdom_messages
            WHERE msg_type =".$msgType." AND battle_state=".$battleState." AND sent_by = :userId";

    $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;  
  }
  public function deleteKingdomRequestedMsgListMsgTypeByRoom($userId, $msgType, $roomId, $battleState, $options=array())
  { 
  
    $sql = "DELETE FROM kingdom_messages
            WHERE msg_type =".$msgType." AND battle_state=".$battleState." AND (room_id=:roomId OR sent_by = :userId)";

    $result = database::doDelete($sql, array('userId'=>$userId, 'roomId'=>$roomId));
    return $result;  
  }
  public function deleteKingdomRequestedMsgListMsgTypeByOpp($userId, $msgType, $oppId, $battleState, $options=array())
  { 
  
    $sql = "DELETE FROM kingdom_messages
            WHERE msg_type =".$msgType." AND battle_state=".$battleState." AND (received_by=:oppId OR sent_by = :userId)";

    $result = database::doDelete($sql, array('userId'=>$userId, 'oppId'=>$oppId));
    return $result;  
  }
  public function deleteKingdomRequestedMsgType($userId,  $msgType, $options=array())
  { 
    $sql = "DELETE FROM kingdom_messages
        WHERE msg_type =".$msgType." AND battle_state=1 AND sent_by = :userId";

    $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;
  }
  //Delete kingdom users, if user dont exists
  public function deleteKingdomUsersIfNotExists($options=array())
  {
    $sql = "DELETE FROM kingdom_users as ku
              WHERE NOT EXISTS(SELECT user_id
                                  FROM user as u
                                  WHERE u.user_id = ku.user_id)";

	  $result = database::doDelete($sql);
    return $result;
  }
  //Delete kingdom users messages, if user dont exists
  public function deleteKingdomUsersMsgIfNotExists($options=array())
  {
    $sql = "DELETE FROM kingdom_messages as km
              WHERE NOT EXISTS(SELECT user_id
                                  FROM user as u
                                  WHERE u.user_id = km.sent_by)";

	  $result = database::doDelete($sql);
    return $result; 
  }
  //Delete kingdom requested messages(only) after 1 day(24 hours)
  public function deleteMsgByDate($options=array())
  {
    $sql = "DELETE FROM kingdom_messages
            WHERE msg_type = 7 AND created_at < NOW() - INTERVAL 1 DAY";

	  $result = database::doDelete($sql);
    return $result;
  }
  public function deleteKindomMsgById($kmId,$options=array())
  {
    $sql = "DELETE FROM kingdom_messages
            WHERE km_id=$kmId";

	  $result = database::doDelete($sql);
    return $result;
  }
  public function deleteKindomMsgByExceptId($currId, $originalId, $options=array())
  {
    $sql = "DELETE FROM kingdom_messages
            WHERE km_id!=$currId AND km_id!=$originalId AND msg_type=2";

	  $result = database::doDelete($sql);
    return $result;
  }
  //Delete kingdom messages after 2 days(48 hours)
  public function deleteKindomMsgByDate($options=array())
  {
    $sql = "DELETE FROM kingdom_messages
            WHERE created_at < NOW() - INTERVAL 2 DAY";

	  $result = database::doDelete($sql);
    return $result;
  }
  //Delete battles history after 7 days
  public function deleteBattleRecordsByDate($options=array())
  {
    /*$sql = "DELETE FROM battle_history
            WHERE created_at < NOW() - INTERVAL 15 DAY";*/
    $sql = "DELETE FROM battle_history
    WHERE created_at < (NOW() - INTERVAL 15 DAY) AND battle_id NOT IN (
      SELECT battle_id
      FROM (
        SELECT *
        FROM battle_history s1
        WHERE (
            SELECT COUNT(*)
            FROM battle_history s2
            WHERE s1.user_id = s2.user_id
                AND s1.created_at <= s2.created_at
        ) <= 20
      ) foo
    )";
	  $result = database::doDelete($sql);
    return $result;
  }
  public function getKingdomUserDetailsOnRelics($kingdomId, $options=array()){
    /*$sql ="SELECT u.*,ROW_NUMBER() OVER (ORDER BY u.user_trophies DESC) AS srno
            FROM kingdom_users u,(SELECT @a:= 0) AS a
            WHERE u.kingdom_id = :kingdomId AND u.user_type > 0 AND u.user_type < 5
            ORDER BY u.user_trophies DESC"; */
    $sql ="SELECT ku.*,ROW_NUMBER() OVER (ORDER BY u.relics DESC) AS srno, u.relics as trophies
            FROM user u
			LEFT JOIN kingdom_users ku ON ku.user_id=u.user_id
            WHERE ku.kingdom_id =:kingdomId AND ku.user_type > 0 AND ku.user_type < 5
            ORDER BY u.relics DESC";
    $result = database::doSelect($sql, array('kingdomId' => $kingdomId));
    return $result;
  }

  public function getKingdomUserTrophiesRelics($kingdomId, $options=array()){
    $sql = "SELECT SUM(u.relics) as trophies
  FROM user u
LEFT JOIN kingdom_users ku ON ku.user_id=u.user_id
  WHERE ku.kingdom_id =:kingdomId AND ku.user_type > 0 AND ku.user_type < 5
  ORDER BY u.relics DESC";
    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId));
    return $result;
  }
  

  public function getKingdomUserRequestedDetailsOnRelics($kingdomId, $options=array()){
    $sql ="SELECT u.*,ROW_NUMBER() OVER (ORDER BY u.user_trophies DESC) AS srno
            FROM kingdom_users u,(SELECT @a:= 0) AS a
            WHERE u.kingdom_id = :kingdomId AND u.user_type<=0
            ORDER BY u.user_trophies DESC"; 
    $result = database::doSelect($sql, array('kingdomId' => $kingdomId));
    return $result;
  }
  public function getKingdomUserKickedDetailsOnRelics($kingdomId, $options=array()){
    $sql ="SELECT u.*,ROW_NUMBER() OVER (ORDER BY u.user_trophies DESC) AS srno
            FROM kingdom_users u,(SELECT @a:= 0) AS a
            WHERE u.kingdom_id = :kingdomId AND u.user_type=9
            ORDER BY u.user_trophies DESC"; 
    $result = database::doSelect($sql, array('kingdomId' => $kingdomId));
    return $result;
  }
  public function getKingdomUserDetailsOnRelicsCount($kingdomId, $options=array()){
    $sql = "SELECT COUNT(*) as cnt
            FROM kingdom_users  
            WHERE user_type!=0 AND user_type!=9 AND kingdom_id IN (:kingdomId)";
 
    //$result = database::doSelect($sql);
    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId));
    return $result['cnt'];
  }
  public function getKingdomBattleByState($userId, $options=array()){
    $sql = "SELECT *
            FROM kingdom_messages  
            WHERE battle_state=1 AND msg_type=3 AND sent_by=:userId";
 
    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result;
  }
  public function getKingdomBattleByStateMsgType($userId, $msgType, $battleState, $options=array()){
    $sql = "SELECT *
            FROM kingdom_messages  
            WHERE battle_state=:battleState AND msg_type=:msgType AND sent_by=:userId";
 
    $result = database::doSelectOne($sql, array('userId' => $userId, 'battleState'=>$battleState, 'msgType'=>$msgType));
    return $result;
  }
  public function getKingdomBattleByStateMsgTypeByOppId($userId, $receivedBy, $msgType, $battleState, $options=array()){
    $sql = "SELECT *
            FROM kingdom_messages  
            WHERE battle_state=:battleState AND msg_type=:msgType AND (sent_by=:userId OR received_by=:receivedBy)";
 
    $result = database::doSelectOne($sql, array('userId' => $userId, 'battleState'=>$battleState, 'msgType'=>$msgType, 'receivedBy'=>$receivedBy));
    return $result;
  }
  public function checkUserAvailable($userId, $options=array()){
    $sql = "SELECT COUNT(*) as cnt
            FROM kingdom_users 
            WHERE user_type > 0 AND user_id IN (:userId)";
 
    //$result = database::doSelect($sql);
    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result['cnt'];
  }
  public function checkUserRequestedCount($userId, $options=array()){
    $sql = "SELECT COUNT(*) as cnt
            FROM kingdom_messages
            WHERE sent_by=:userId AND msg_type=7";
 
    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result['cnt'];
  }
  public function getKingdomUsersRequestedCount($userId, $options = array())
  {
    $sql = "SELECT COUNT(*) AS cnt
            FROM kingdom_messages
            WHERE sent_by=:userId AND msg_type=7";

    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result['cnt'];
  }
  public function checkUserRequestedAvailable($userId, $kingdomId, $options=array()){
    $sql = "SELECT COUNT(*) as cnt
            FROM kingdom_users 
            WHERE kingdom_id=:kingdomId AND user_id IN (:userId)";
 
    //$result = database::doSelect($sql);
    $result = database::doSelectOne($sql, array('userId' => $userId, 'kingdomId' => $kingdomId));
    return $result['cnt'];
  }
  
  public function checkKingdomAlreadyExisted($kingdomName, $options=array()){
    $sql = "SELECT COUNT(*) as cnt
            FROM kingdom 
            WHERE kingdom_name IN (:kingdomName)";
 
    //$result = database::doSelect($sql);
    $result = database::doSelectOne($sql, array('kingdomName' => $kingdomName));
    return $result['cnt'];
  }
  public function getKingdomUsersList($kingdomId){
    $sql = "SELECT *
            FROM kingdom_users
            WHERE kingdom_id = :kingdomId"; 

	  $result = database::doSelect($sql, array('kingdomId' => $kingdomId));
    return $result;
  }
  public function getKingdomUsersRequestedList($kingdomId){
    $sql = "SELECT *
            FROM kingdom_users
            WHERE kingdom_id = :kingdomId AND user_type<=0"; 

	  $result = database::doSelect($sql, array('kingdomId' => $kingdomId));
    return $result;
  }
  public function getKingdomDetails($kingdomId, $options=array()){
    $sql = "SELECT *
            FROM kingdom 
            WHERE kingdom_id =:kingdomId";

    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId));
    return $result;
  }
  public function checkKingdomPermission($kingdomId, $userId, $options=array()){
    $sql = "SELECT COUNT(ku.user_id) AS present
            FROM kingdom k
            LEFT JOIN kingdom_users ku ON ku.kingdom_id=k.kingdom_id
            WHERE k.kingdom_id=:kingdomId AND ku.user_id = :userId AND ku.user_type>= 2";

    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId, 'userId' => $userId));
    return $result['present'];
  }
  
  public function getKingdomTotalRelics($kingdomId, $options=array()){
    /*$sql = "SELECT SUM(user_trophies) AS total, SUM(donation) as total_donation
            FROM kingdom_users
            WHERE kingdom_id=:kingdomId";*/
    $sql = "SELECT SUM(u.relics) AS total, SUM(ku.donation) AS total_donation
            FROM kingdom_users AS ku
            LEFT JOIN user AS u ON u.user_id=ku.user_id
            WHERE ku.user_type!=0 AND user_type!=9 AND ku.kingdom_id=:kingdomId";
    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId));
    return $result;
  }
  public function getKingdomUsersCount($kingdomId, $options=array()){
    $sql = "SELECT COUNT(*) AS cnt
            FROM kingdom_users
            WHERE kingdom_id=:kingdomId";

    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId));
    return $result['cnt'];
  }
  public function getKingdomUsersCountwithoutRequested($kingdomId, $options=array()){
    $sql = "SELECT COUNT(*) AS cnt
            FROM kingdom_users
            WHERE kingdom_id=:kingdomId AND user_type!=0 AND user_type!=9";

    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId));
    return $result['cnt'];
  }
  public function getCheckMsgAvailableCount($kingdomId, $options=array()){
    $sql = "SELECT COUNT(*) AS cnt
            FROM kingdom_messages
            WHERE kingdom_id =:kingdomId AND battle_state!=5 AND battle_state!=4";

    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId));
    return $result['cnt'];
  }
  public function getKingdomUnseenMsgCount($kingdomId,$last_id, $userId, $options=array()){
    $sql = "SELECT COUNT(*) AS cnt
            FROM kingdom_messages
            WHERE kingdom_id =:kingdomId AND km_id >:last_id AND sent_by!=:userId AND battle_state!=5 AND battle_state!=4";

    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId, 'last_id'=>$last_id, 'userId'=>$userId));
    return $result['cnt'];
  }
  public function getKingdomUserDetailsWithUsersId($userId, $options=array()){
    $sql = "SELECT *
            FROM kingdom_users
            WHERE user_id=:userId";

    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result;
  } 
  public function getKingdomUserDetailsWithRequestUsersId($userId,$kingdomId, $options=array()){
    $sql = "SELECT *
            FROM kingdom_users
            WHERE user_id=:userId AND kingdom_id=:kingdomId";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'kingdomId' => $kingdomId));
    return $result;
  } 
  public function getKingdomUserDetailsUsersId($userId, $options=array()){
    $sql = "SELECT *
            FROM kingdom_users
            WHERE user_id=:userId";

    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result;
  } 
   
  public function deleteKingdomUser($userId, $options=array())
  {
    $sql = "DELETE FROM kingdom_users
            WHERE user_id = :userId";

	  $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;
  }
  public function deleteKingdomRequestedUser($userId, $kingdomId=0, $action, $options=array())
  { 
    if($action==1){
      $sql = "DELETE FROM kingdom_users
              WHERE kingdom_id !=".$kingdomId." AND user_id = :userId";
    }else{
      $sql = "DELETE FROM kingdom_users
              WHERE user_id = :userId AND kingdom_id=".$kingdomId;
    }
    $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;
  }
  public function deleteKingdom($kingdomId, $options=array())
  {
    $sql = "DELETE FROM kingdom
            WHERE kingdom_id = :kingdomId";

	  $result = database::doDelete($sql, array('kingdomId'=>$kingdomId));
    return $result;
  }
  public function getLatestKingdomUserDetails($kingdomId, $options=array()){
    $sql = "SELECT *
            FROM kingdom_users
            WHERE kingdom_id=:kingdomId
            ORDER BY created_at ASC";

    $result = database::doSelectOne($sql, array('kingdomId' => $kingdomId));
    return $result; 
  }
  public function getKingdomListWithRankOnSearch($searchName, $options=array())
  {
    $sql = "SELECT k.*, SUM(ku.user_trophies) AS user_trophy, ROW_NUMBER() OVER (ORDER BY SUM(ku.user_trophies) DESC) AS srno
            FROM kingdom k
            LEFT JOIN kingdom_users ku ON ku.kingdom_id=k.kingdom_id
            WHERE (k.kingdom_name LIKE '".$searchName."%' OR k.kingdom_name LIKE '%".$searchName."%')
            GROUP BY k.kingdom_id";
    $result = database::doSelect($sql, array('searchName' => $searchName));
    return $result;
  }
  public function getKingdomListSearch($searchName, $kingdomType, $requiredCups, $reqWarrior=1, $options=array())
  {
    $sql = "SELECT k.*, COUNT(*) as total_warriors,ROW_NUMBER() OVER (ORDER BY SUM(ku.user_trophies) DESC) AS srno
    FROM kingdom_users ku
    JOIN kingdom k ON k.kingdom_id=ku.kingdom_id 
    WHERE "; 
    /*foreach($options as $key=>$value){
      $sql .= $key." like %".$value."% AND ";
    }*/
    if($searchName != "")
    {
      $sql .= "(k.kingdom_name LIKE '".$searchName."%' OR k.kingdom_name LIKE '%".$searchName."%') ";
    }
    if($kingdomType != "" && $kingdomType > 0)
    { 
      if($searchName != ""){
        $sql .= " AND "; 
      }
      $sql .= "k.kingdom_type =".$kingdomType;
    }
    if($requiredCups != "" && $requiredCups >= 0)
    { 
      if($searchName != "" || $kingdomType!=""){
        $sql .= " AND "; 
      }
      //$sql .= " AND "; 
      $sql .= "k.kingdom_req_cup_amt >=".$requiredCups;
    }
   
    $sql .= " GROUP BY k.kingdom_id";
   if($reqWarrior != "" && $reqWarrior > 0)
    {
      $sql .= " HAVING total_warriors >=".$reqWarrior;
    }
    
    $sql = rtrim($sql, " AND ");
    print_log($sql);
    $result = database::doSelect($sql, $options);
    return $result;
  }

  // public function getMatchingPlayer($waitingRoomId, $userId, $levelId, $relics, $masterStadiumId, $options=array())
  // {
  //   $sql =  "SELECT waiting_room.waiting_room_id, user.relics, user.level_id, waiting_room.status FROM waiting_room
  //           INNER JOIN user ON user.user_id = waiting_room.user_id
  //           WHERE  waiting_room.status = :pending AND user.level_id = :levelId AND user.master_stadium_id = :masterStadiumId AND waiting_room.user_id <> :userId
  //           AND waiting_room.waiting_room_id <> :waitingRoomId
  //           AND entry_time > :minWaitingTime
  //           ORDER BY  user.relics - :relics";
  //
  //   $minWaitingTime = time() - ROOM_SEARCH_TIMEOUT_TIME;
  //
  //   $result = database::doSelectOne($sql, array('waitingRoomId' => $waitingRoomId, 'userId' => $userId, 'levelId' => $levelId, 'minWaitingTime' => $minWaitingTime, 'relics' => $relics, 'pending' => CONTENT_PENDING, 'masterStadiumId' => $masterStadiumId));
  //   return $result;
  // }

  //dont consider level here
  public function getMatchingPlayer($waitingRoomId, $userId,  $relics, $masterStadiumId, $options=array())
  {
    $sql =  "SELECT waiting_room.waiting_room_id, user.relics, user.level_id, waiting_room.status FROM waiting_room
            INNER JOIN user ON user.user_id = waiting_room.user_id
            WHERE  waiting_room.status = :pending AND user.master_stadium_id = :masterStadiumId AND waiting_room.user_id <> :userId
            AND waiting_room.waiting_room_id <> :waitingRoomId
            AND entry_time > :minWaitingTime
            ORDER BY  user.relics - :relics";

    $minWaitingTime = time() - ROOM_SEARCH_TIMEOUT_TIME;

    $result = database::doSelectOne($sql, array('waitingRoomId' => $waitingRoomId, 'userId' => $userId, 'minWaitingTime' => $minWaitingTime, 'relics' => $relics, 'pending' => CONTENT_PENDING, 'masterStadiumId' => $masterStadiumId));
    return $result;
  }

  public function updateWaitingRoom($waitingRoomId, $options=array())
  {
    $sql = "UPDATE waiting_room SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE waiting_room_id =:waitingRoomId";
    $options['waitingRoomId'] = $waitingRoomId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function insertWaitingRoomPlayer($options=array())
  {
    $sql = "INSERT INTO waiting_room ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getWaitingRoomDetail($waitingRoomId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            WHERE waiting_room_id = :waitingRoomId";

    $result = database::doSelectOne($sql, array('waitingRoomId'=>$waitingRoomId));
    return $result;
  }

  public function getPlayersForRoomId($roomId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            INNER JOIN user ON user.user_id = waiting_room.user_id
            WHERE room_id = :roomId";

    $result = database::doSelect($sql, array('roomId'=>$roomId));
    return $result;
  }

  public function getPercentageofCubewithMasterId($masterId){
    $sql = "SELECT cube_id,percentage 
            FROM master_cube_probability 
            WHERE master_stadium_id=:masterId AND percentage <> 0";
    $result = database::doSelect($sql, array('masterId'=>$masterId));
    return $result;
  }
  public function getCubeSequenceMaxPosDetails($userId, $seqId){
    $sql = "SELECT MAX(seq_pos_id) as seq_pos_id
            FROM user_reward
            WHERE user_id=:userId AND seq_id=:seqId";
     $result = database::doSelect($sql, array('userId'=>$userId, 'seqId'=> $seqId));
    return $result;
  } 
  public function getSequenceCubeId($seqId, $seqPosId){
    $sql = "SELECT cube_id 
            FROM cube_sequence 
            WHERE seq_id=:seqId AND seq_pos_id=:seqPosId";
     $result = database::doSelect($sql, array('seqPosId'=>$seqPosId, 'seqId'=> $seqId));
    return $result;
  }

  public function updateWaitingRoomForPlayerResult($roomId, $userId, $options=array())
  {
    $sql = "UPDATE waiting_room SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE room_id =:roomId AND user_id =:userId";
    $options['roomId'] = $roomId;
    $options['userId'] = $userId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function getUserRewardCircletCount($userId, $createdAt, $options=array())
  {
    $sql = "SELECT SUM(circlet) AS sum_of_circlet
            FROM waiting_room
            WHERE user_id = :userId  AND entry_time > :createdAt";

    $result = database::doSelectOne($sql, array('userId'=>$userId, 'createdAt'=>$createdAt));
    return $result;
  }

  public function formatMatchingPlayer($roomPlayers, $options=array())
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');

    $userDetailList = array();

    foreach($roomPlayers as $player)
    {
      $temp = array();
      $userDetail = $userLib->getUserDetail($player['user_id']);

      $temp['user_id'] = $player['user_id'];
      $temp['name'] = $player['name'];
      $temp['level_id'] = $player['level_id'];
      $temp['is_ai'] = ($userDetail['is_ai'] == CONTENT_ACTIVE) ? true : false;
      if($temp['is_ai']){
        if($userDetail['ai_deck_id'] == 3){
          $temp['difficulty'] = rand(1, 2);
        } else if(in_array($userDetail['ai_deck_id'], NORMAL_AI_DECK)){
          $temp['difficulty'] = AI_DECK_NORMAL;
        } else {
          $temp['difficulty'] = AI_DECK_DIFFICULT;
        }
      } else {
        $temp['difficulty'] = 0;
      }
      $deckList = $cardLib->getUserCardDeckList($player['user_id']);
      $deckCard = array();
      foreach($deckList as $card)
      {
        $cardPropertyInfo = $tempDeck = array();
        $tempDeck['user_card_id'] = $card['user_card_id'];
        $tempDeck['master_card_id'] = $card['master_card_id'];
        $tempDeck['title'] = $card['title'];
        $tempDeck['card_type'] = $card['card_type'];
        $tempDeck['card_type_message'] = ($card['card_type'] == CARD_TYPE_CHARACTER)?"Character":"Power";
        $tempDeck['card_rarity_type'] = $card['card_rarity_type'];
        $tempDeck['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":"Ultra Rare");
        $tempDeck['is_deck_message'] = ($card['is_deck'] == CONTENT_ACTIVE)?"in deck":"not in deck";
        $tempDeck['is_deck'] = $card['is_deck'];
        $cardLevelUpDetail = $cardLib->getMasterCardLevelUpgradeForCardCount($card['level_id']+1, $card['card_rarity_type']);
        $tempDeck['next_level_card_count'] = $cardLevelUpDetail['card_count'];
        $tempDeck['next_level_gold_cost'] = $cardLevelUpDetail['gold'];
        $tempDeck['total_card'] = $card['user_card_count'];
        $tempDeck['card_level'] = $card['level_id'];
        $tempDeck['card_description'] = $card['card_description'];

        $cardPropertyList = $cardLib->getCardPropertyForUseCardId($card['user_card_id']);
        foreach($cardPropertyList as $cardProperty)
        {
          $tempProperty = array();
          if($cardProperty['is_default'] == CONTENT_ACTIVE){
            $tempDeck[$cardProperty['property_id']] = $cardProperty['user_card_property_value'];
          } else
          {
            $tempProperty['property_id'] = $cardProperty['property_id'];
            $tempProperty['property_name'] = $cardProperty['property_name'];
            $tempProperty['property_value'] = $cardProperty['user_card_property_value'];
            $propertyValue = $cardLib->getCardPropertyValue($card['master_card_id'], $card['level_id']+1, $cardProperty['card_property_id']);
            $tempProperty['property_update_bonus'] = !empty($propertyValue['card_property_value'])?$propertyValue['card_property_value']-$tempProperty['property_value']:0;
            $cardPropertyInfo[] = $tempProperty;
          }
        }
        $tempDeck['property_list'] = $cardPropertyInfo;
        $deckCard[] = $tempDeck;
      }
      $temp['deck_list'] = $deckCard;

      $userDetailList[] = $temp;
    }

    return $userDetailList;
  }

  public function getWaitingPlayerBasedOnActiveStatus($userId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            WHERE status <> :closed AND user_id =:userId
            ORDER BY waiting_room_id DESC";

    $result = database::doSelectOne($sql, array('closed' => CONTENT_CLOSED,  'userId' => $userId));
    return $result;
  }

  public function updateWaitingRoomStatus($roomId, $userId, $options=array())
  {
    $sql = "UPDATE waiting_room SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE room_id =:roomId AND user_id =:userId";
    $options['roomId'] = $roomId;
    $options['userId'] = $userId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function getWaitingRoomActiveForRoom($userId, $roomId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            WHERE status <> :closed AND user_id = :userId AND room_id = :roomId";

    $result = database::doSelectOne($sql, array('closed' => CONTENT_CLOSED, 'roomId' => $roomId, 'userId' => $userId));
    return $result;
  }

  public function getOpponentRoomUserForRoomAndUser($userId, $roomId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            INNER JOIN user ON user.user_id = waiting_room.user_id
            WHERE waiting_room.user_id <> :userId AND room_id = :roomId";

    $result = database::doSelectOne($sql, array('roomId' => $roomId, 'userId' => $userId));
    return $result;
  }

  public function getWaitingRoomContinuesWinCount($userId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            WHERE  user_id = :userId
            ORDER BY created_at DESC";

    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }

  public function getRoomPlayedListForUser($userId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            WHERE  user_id = :userId AND room_id > 0";

    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }

  public function getPreviousWaitingRoomDetail($userId, $roomId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            WHERE user_id = :userId AND room_id <> :roomId AND room_id > 0
            ORDER BY waiting_room_id DESC";

    $result = database::doSelectOne($sql, array('userId'=>$userId, 'roomId' =>$roomId));
    return $result;
  }

  public function getUserWinStreak($userId, $options=array())
  {
    $sql = "SELECT MAX(win_streak) as win_streak
            FROM waiting_room
            WHERE user_id = :userId";

    $result = database::doSelectOne($sql, array('userId'=>$userId));
    return $result;
  }

  public function getTotalWinStatusToUser($userId, $winStatus, $options=array())
  {
    $sql = "SELECT count(*) as win_count
            FROM waiting_room
            WHERE user_id = :userId AND win_status=:winStatus";

    $result = database::doSelectOne($sql, array('userId'=>$userId, 'winStatus' => $winStatus));
    return $result;
  }

  public function checkUserExists($userId, $kingdomId, $options=array())
  {
    $sql = "SELECT * 
            FROM kingdom_users
            WHERE kingdom_id=:kingdomId AND user_type!=0 AND user_id= :userId";

    $result = database::doSelect($sql, array('userId' => $userId, 'kingdomId' => $kingdomId));
    return $result;
  }
}