<?php
class room{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getRoomList($options=array())
  {
    $sql = "SELECT *
            FROM room";

    $result = database::doSelect($sql);
    return $result;
  }

  public function insertRoom($options=array())
  {
    $sql = "INSERT INTO room ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function updateRoom($roomId, $options=array())
  {
    $sql = "UPDATE room SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE room_id =:roomId";
    $options['roomId'] = $roomId;

    $result = database::doUpdate($sql, $options);
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
  /*public function getMatchingPlayer($waitingRoomId, $userId,  $relics, $masterStadiumId, $options=array())
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
*/
public function getMatchingPlayer($waitingRoomId, $userId,  $levelId, $relics, $masterStadiumId, $options=array())
  {
    date_default_timezone_set('Asia/Kolkata');
    $sql =  "SELECT waiting_room.waiting_room_id, user.relics, user.level_id, waiting_room.status 
            FROM waiting_room
            INNER JOIN user ON user.user_id = waiting_room.user_id
            WHERE waiting_room.status = :pending AND user.master_stadium_id = :masterStadiumId AND waiting_room.user_id <> :userId AND waiting_room.waiting_room_id <> :waitingRoomId
            AND entry_time > :minWaitingTime
            ORDER BY  user.relics - :relics";
//AND (user.level_id=:levelId OR user.level_id=:levelIdAdd OR user.level_id=:levelIdSub)
//'levelId' => $levelId,
 //                                               'levelIdAdd' => $levelIdAdd,
   //                                             'levelIdSub'=>$levelIdSub, 
    $minWaitingTime = time() - ROOM_SEARCH_TIMEOUT_TIME;
    $levelIdAdd= $levelId+1;
    $levelIdSub= $levelId-1;
    $result = database::doSelectOne($sql, array('waitingRoomId' => $waitingRoomId, 
                                                'userId' => $userId, 
                                                'minWaitingTime' => $minWaitingTime, 
                                                'relics' => $relics, 
                                                'pending' => CONTENT_PENDING, 
                                                'masterStadiumId' => $masterStadiumId));
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
  public function getRoomDetailForFriendlyBattle($userId, $options=array())
  {
    $sql = "SELECT *
            FROM room
            WHERE user_id = :userId
            ORDER BY room_id DESC, created_at DESC
            LIMIT 1";

    $result = database::doSelectOne($sql, array('userId'=>$userId));
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
  public function getDetailForFriendlyBattleWithUserId($userId, $options=array())
  {
    $sql = "SELECT * 
            FROM friendly_invite
            WHERE user_id=:userId
            ORDER BY created_at DESC
            LIMIT 1";

    $result = database::doSelectOne($sql, array('userId'=>$userId, 'roomId'=>$roomId));
    return $result['accepted_user_id'];
  }
  public function getOppPlayerForUser($userId, $waitingRoomId){
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    //get user detail
    $userDetail = $userLib->getUserDetail($userId);
    //check if any free AI is present with matching conditions
    $oppUserId = $this->getDetailForFriendlyBattleWithUserId($userId);
    

    $roomId = $roomLib->insertRoom(array(
      'user_id' => $userId,
      'created_at' => date('Y-m-d H:i:s'),
      'status' => CONTENT_ACTIVE));

    //Assign the room to battling player.
    $roomLib->updateWaitingRoom($waitingRoomId, array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));

    $roomLib->insertWaitingRoomPlayer(array(
      'user_id' => $oppUserId,
      'room_id' => $roomId,
      'win_status' => BATTLE_DEFAULT_STATUS,
      'entry_time' => time(),
      'created_at' => date('Y-m-d H:i:s'),
      'status' => CONTENT_ACTIVE));

    return $roomId;
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
     $result = database::doSelectOne($sql, array('userId'=>$userId, 'seqId'=> $seqId));
    return $result;
  } 
  public function getSequenceCubeId($seqId, $seqPosId){
    $sql = "SELECT cube_id 
            FROM cube_sequence 
            WHERE seq_id=:seqId AND seq_pos_id=:seqPosId";
     $result = database::doSelectOne($sql, array('seqPosId'=>$seqPosId, 'seqId'=> $seqId));
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
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');

    $userDetailList = array();

    foreach($roomPlayers as $player)
    {
      $temp = array();
      $userDetail = $userLib->getUserDetail($player['user_id']);

      $temp['user_id'] = $player['user_id'];
      $temp['name'] = $player['name'];
      $temp['level_id'] = $player['level_id'];
      $temp['kingQueen_status'] = $player['kingQueen_status'];
      $temp['is_ai'] = ($userDetail['is_ai'] == CONTENT_ACTIVE) ? true : false;
      $kingdomDetails= $kingdomLib->getKingdomDetails($userDetail['kingdom_id']);
      if($temp['is_ai']==false && $userDetail['kingdom_id']>0){
        $temp['kingdom_id']= $kingdomDetails['kingdom_id'];
        $temp['kingdom_name']= $kingdomDetails['kingdom_name'];
      }
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
        $tempDeck['bundlename'] = $card['bundlename'];
        $tempDeck['android_bundlehash']=$card['android_bundlehash'];
        $tempDeck['android_bundlecrc']=$card['android_bundlecrc'];
        $tempDeck['ios_bundlehash']=$card['ios_bundlehash'];
        $tempDeck['ios_bundlecrc']=$card['ios_bundlecrc'];
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

  public function formatMatchingPlayerwithType($roomPlayers, $options=array())
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');

    $userDetailList = array();

    foreach($roomPlayers as $player)
    {
      $temp = array();
      $cardBattleLevel=5;
      $userDetail = $userLib->getUserDetail($player['user_id']);

      $temp['user_id'] = $player['user_id'];
      $temp['name'] = $player['name'];
      $temp['level_id'] = $cardBattleLevel;
      $temp['stadiumid'] = 1; //master stadium for invite
      $temp['kingQueen_status'] = $player['kingQueen_status'];
      $temp['is_ai'] = ($userDetail['is_ai'] == CONTENT_ACTIVE) ? true : false;
      $kingdomDetails= $kingdomLib->getKingdomDetails($userDetail['kingdom_id']);
      if($temp['is_ai']==false && $userDetail['kingdom_id']>0){
        $temp['kingdom_id']= $kingdomDetails['kingdom_id'];
        $temp['kingdom_name']= $kingdomDetails['kingdom_name'];
      }
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
        $tempDeck['bundlename'] = $card['bundlename'];
        $tempDeck['android_bundlehash']=$card['android_bundlehash'];
        $tempDeck['android_bundlecrc']=$card['android_bundlecrc'];
        $tempDeck['ios_bundlehash']=$card['ios_bundlehash'];
        $tempDeck['ios_bundlecrc']=$card['ios_bundlecrc'];
        $tempDeck['card_type_message'] = ($card['card_type'] == CARD_TYPE_CHARACTER)?"Character":"Power";
        $tempDeck['card_rarity_type'] = $card['card_rarity_type'];
        $tempDeck['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":"Ultra Rare");
        $tempDeck['is_deck_message'] = ($card['is_deck'] == CONTENT_ACTIVE)?"in deck":"not in deck";
        $tempDeck['is_deck'] = $card['is_deck'];
        $cardLevelUpDetail = $cardLib->getMasterCardLevelUpgradeForCardCount($card['level_id']+1, $card['card_rarity_type']);
        $tempDeck['next_level_card_count'] = $cardLevelUpDetail['card_count'];
        $tempDeck['next_level_gold_cost'] = $cardLevelUpDetail['gold'];
        $tempDeck['total_card'] = $card['user_card_count'];
        $tempDeck['card_level'] = $cardBattleLevel;
        $tempDeck['card_description'] = $card['card_description'];
         
        switch($card['card_rarity_type']){
          case 4: 
            $basic_lvl=7;
            break;
          case 3:
            $basic_lvl=4;
            break;
          case 2:
            $basic_lvl=2;
            break;
          default:
            $basic_lvl=1; 
        }
      //$cardPropertyList = $cardLib->getCardPropertyForUseCardId($card['user_card_id']); 
        $cardPropertyList = $cardLib->getCardPropertyForMasterCardAndLevelIdAndCommonLevel($card['master_card_id'], $cardBattleLevel, $basic_lvl);

        foreach($cardPropertyList as $cardProperty)
        {
          $tempProperty = array();
          if($cardProperty['is_default'] == CONTENT_ACTIVE){
            //$tempDeck[$cardProperty['property_id']] = $cardProperty['user_card_property_value'];
            $tempDeck[$cardProperty['property_id']] = $cardProperty['card_property_value'];
          } else
          {
            $tempProperty['property_id'] = $cardProperty['property_id'];
            $tempProperty['property_name'] = $cardProperty['property_name'];
            //$tempProperty['property_value'] = $cardProperty['user_card_property_value'];
            $tempProperty['property_value'] = $cardProperty['card_property_value'];
            $propertyValue = $cardLib->getCardPropertyValue($card['master_card_id'], $cardBattleLevel+1, $cardProperty['card_property_id']);
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
  public function matchingPlayerDetails($userId, $oppId, $usrDckLst, $oppDckLst, $options=array())
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');

    $userDetailList = array();
    //$batHist = $userLib->getBattleHistoryList($userId);
    /*foreach($batHist as $bhLst) {
      $usrDckLst=$bhLst['userDeckLst']; 
      $oppDckLst =$bhLst['oppDeckLst']; 
    }*/ 
    
    $a = array($userId=>$usrDckLst,
               $oppId=>$oppDckLst);
    foreach($a as $player => $players_val)
    {
      $temp = array();
      $userDetail = $userLib->getUserDetail($player);
      $kingdomDetails = $kingdomLib->getKingdomDetails($userDetail['kingdom_id']);
      $temp['user_id'] = $userDetail['user_id'];
      $temp['name'] = $userDetail['name'];
      $temp['level_id'] = $userDetail['level_id'];
      $temp['facebook_id'] = $userDetail['facebook_id'];
      $temp['trophies'] = $userDetail['relics'];
      $temp['shield_id']=empty($kingdomDetails['kingdom_shield_id'])?0:$kingdomDetails['kingdom_shield_id'];
      $temp['deck_list']=json_decode($players_val);
     // $deckList = $cardLib->getUserCardDeckList($player);
      /*$deckCard = array();
      foreach(json_decode($players_val) as $card)
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
        $tempDeck['card_level'] = $card['level_id'];
        $deckCard[] = $tempDeck;
      } 
      $temp['deck_list'] = $deckCard;
*/
      $userDetailList[] = $temp;
    }

    return $userDetailList;
  }
  public function getMatchPlayersTrophies($win_status, $masterStadiumId, $options=array())
  {
    $sql =  "SELECT *
            FROM master_match_status_reward
            WHERE master_stadium_id<=:masterStadiumId AND win_status=:win_status
            ORDER BY master_stadium_id DESC
            LIMIT 1";

    $result = database::doSelectOne($sql, array('win_status' => $win_status, 'masterStadiumId' => $masterStadiumId));
    return $result['relics'];
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
  public function getWaitingPlayerBasedOnUserId($userId, $options=array())
  {
    $sql = "SELECT *
            FROM waiting_room
            WHERE user_id =:userId
            ORDER BY waiting_room_id DESC";

    $result = database::doSelectOne($sql, array( 'userId' => $userId));
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
  public function deleteAllWaitingRoomUser($userId, $waitingRoomId, $options=array())
  {
    $sql = "DELETE FROM waiting_room
            WHERE user_id = :userId AND waiting_room_id =:waitingRoomId";

	  $result = database::doDelete($sql, array('userId'=>$userId, 'waitingRoomId'=>$waitingRoomId));
    return $result;
  }
  public function deleteAllWaitingRoomUserId($userId, $oppUserId, $roomId, $options=array())
  {
    $sql = "DELETE u1 FROM waiting_room u1 
		INNER JOIN waiting_room u2 ON u1.waiting_room_id < u2.waiting_room_id AND u1.user_id = u2.user_id";
    

    /*DELETE FROM waiting_room
            WHERE user_id = :userId OR user_id=:oppUserId AND room_id !=:roomId
            ORDER BY waiting_room_id DESC
            LIMIT 2";*/

	  $result = database::doDelete($sql, array('userId'=>$userId, 'oppUserId'=>$oppUserId, 'roomId'=>$roomId));
    return $result;
  }
}
