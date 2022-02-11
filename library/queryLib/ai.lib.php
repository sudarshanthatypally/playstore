<?php
class ai{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getAiPlayerForUser($userId, $waitingRoomId){
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    //get user detail
    $userDetail = $userLib->getUserDetail($userId);
    //check if any free AI is present with matching conditions
    $aiUserId = $this->getAvailableAiForUser($userDetail['level_id'], $userDetail['master_stadium_id'], $userDetail['relics']);
    $deckId =0;
    //create a new AI
    if(!$aiUserId){
      $deckId = $this->getDeckForUserLevel($userDetail['level_id']);
      $aiUserId = $this->addNewAi($userDetail['level_id'], $userDetail['master_stadium_id'], $userDetail['relics'], $deckId);
    } else {
      $userLib->updateUser($aiUserId, array('is_ai_available' => CONTENT_INACTIVE, 'ai_game_started_at' => date('Y-m-d H:i:s')));
    }

    $roomId = $roomLib->insertRoom(array(
      'user_id' => $userId,
      'created_at' => date('Y-m-d H:i:s'),
      'status' => CONTENT_ACTIVE));

    //Assign the room to battling player.
    $roomLib->updateWaitingRoom($waitingRoomId, array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));

    $roomLib->insertWaitingRoomPlayer(array(
      'user_id' => $aiUserId,
      'room_id' => $roomId,
      'win_status' => BATTLE_DEFAULT_STATUS,
      'entry_time' => time(),
      'created_at' => date('Y-m-d H:i:s'),
      'status' => CONTENT_ACTIVE));

    if($deckId > 0)
      $cardList = $this->assignCardsToAI($aiUserId, $deckId);

    return $roomId;
  }

  public function getAvailableAiForUser($level, $stadiumId, $relicCount){
    $possibleDecks = $this->getPossibleDecksForUserLevel($level);
    $deckIdList = array_column($possibleDecks, 'deck_id');

    $availableAiList = $this->checkForAvailableAi($level, $stadiumId, $relicCount, implode(',', $deckIdList));

    if(empty($availableAiList))
      return 0;

    $selectedAi = $availableAiList[array_rand($availableAiList)];
    return $selectedAi['user_id'];
  }

  public function getDeckForUserLevel($level){
    $possibleDecks = $this->getPossibleDecksForUserLevel($level);
    $deckIdList = array_column($possibleDecks, 'deck_id');
    return $deckIdList[array_rand($deckIdList)];
  }

  public function assignCardsToAI($userId, $deckId){
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $userDetails = $userLib->getUserDetail($userId);
    /*foreach ($userDetails as $user) {
      $userStadiumId = $user['master_stadium_id'];
    } */
    $userStadiumId = $userDetails['master_stadium_id'];
    //$cardList = $this->getCardListForDeck($deckId);
    $cardList = $this->getCardIdWithDeckAndMasterCard($userStadiumId, $deckId);

    $dbDeckLevelRatioInfo = $this->getDeckLevelAndRatioInfoForDeck($deckId);
    $levelRatioInfo = $this->getMinMaxDefinedArrayForProbability($dbDeckLevelRatioInfo);
    $res=array();
    foreach ($cardList as $cardListId) {
      $i=0;
      $tempi = array();
      $tempi[$i++] = $cardListId['master_card_id'];
      $res[] = implode(',', $tempi);
    }
    //$cardPrevList = $this->getCardPrevious($userId);
    $deckCardList=$res;
    $cardPrevList = $this->getUnlockedCardsList($userStadiumId, $deckId);
    $resCard = array();
    foreach ($cardPrevList as $cplId) {
      $j=0;
      $tempCard = array();
      $tempCard[$j++] = $cplId['master_card_id'];
      $resCard[] = implode(',', $tempCard);
    }
    $checkPreviousCardList=$resCard; 
    	$rank_b=1;
    	$rank_s=1; 
    	$rank_t=1;
      foreach ($cardList as $card) {
          $cardId = $card['master_card_id'];
          //print_log('test'); 
          /*if($card['master_stadium_id'] <= $userStadiumId && (in_array($card['master_card_id'], $checkPreviousCardList)!=1)){
    		  	$cardId = $card['master_card_id'];
    	  }else{
          	if($card['card_type']==1){
          		$cardId=$this->getUnlockedCardsListbyCardType($userStadiumId, $deckId, $rank_t, $card['card_type']);
          		if(in_array($cardId, $deckCardList)){
          			$cardId=$this->getUnlockedCardsListbyCardType($userStadiumId, $deckId, $rank_t, $card['card_type']);	
          		}
          		$rank_t++;
    	    }elseif($card['card_type']==2){
    	    	$cardId=$this->getUnlockedCardsListbyCardType($userStadiumId, $deckId, $rank_s, $card['card_type']);
    	    	if(in_array($cardId, $deckCardList)){
    	    		$cardId=$this->getUnlockedCardsListbyCardType($userStadiumId, $deckId, $rank_s+1, $card['card_type']);
    	    	}
    	    	$rank_s++;
    	    }elseif($card['card_type']==3){
    	    	$cardId=$this->getUnlockedCardsListbyCardType($userStadiumId, $deckId, $rank_b, $card['card_type']);
    	    	if(in_array($cardId, $deckCardList)){
    	    		$cardId=$this->getUnlockedCardsListbyCardType($userStadiumId, $deckId, $rank_b+1, $card['card_type']);
    	    	}
    	    	$rank_b++;
    	    }else{
            $ctval=1;
    	    	$cardId=$this->getUnlockedCardsListbyCardType($userStadiumId, $deckId, $rank_t, $ctval+1);
          		if(in_array($cardId, $deckCardList)){
          			$cardId=$this->getUnlockedCardsListbyCardType($userStadiumId, $deckId, $rank_s, $ctval);	
          			$rank_s++;
          		}
          		$rank_t++;
    	    }  	
        } 
      
       if(empty($cardId) || count($cardId)==0 || $cardId==0){
		      	$getCardMSIds = $cardLib->getCardFromMasterStadiumId($userStadiumId); 
		        if(!empty($getCardMSIds)){
		          foreach ($getCardMSIds as $getCardMSId) {
		            if((in_array($getCardMSId['master_card_id'], $deckCardList)!=1) && (in_array($getCardMSId['master_card_id'], $checkPreviousCardList)!=1)){
		              		$cardId=$getCardMSId['master_card_id'];
		          	} 
		          }
		      	} 
		      } */
      $randProb = rand(0, 100);
      $cardLevel = $this->getLevelForProbability($randProb, $levelRatioInfo);
      $opponentCardLevel = $cardLib->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($cardId);
      //print_log($opponentCardLevel['level_id']);
      //print_log($cardLevel);
      if($opponentCardLevel['level_id'] <= $userDetails['level_id']){
        $cardlvl=$userDetails['level_id'];
      } 
      else{
        $cardlvl=$opponentCardLevel['level_id'];
      }
      //print_log($cardlvl);
      //insert card with above level
      $userCardId = $cardLib->insertUserCard(array(
                      'user_id' => $userId,
                      'master_card_id' => $cardId,
                      'is_deck' => CONTENT_ACTIVE,
                      'level_id' => (empty($cardlvl))?DEFAULT_CARD_LEVEL_ID:$cardlvl,
                      'user_card_count' => DEFAULT_CARD_COUNT,
                      'created_at' => date('Y-m-d H:i:s'),
                      'status' => CONTENT_ACTIVE ));

      $cardPropertyList = $cardLib->getMasterCardPropertyList($cardId);
      //print_log($cardPropertyList);
      foreach($cardPropertyList as $cardProperty)
      {
        /*$cardPropertyValue = $cardLib->getCardPropertyValue($cardId, $cardlvl, $cardProperty['card_property_id']);
        print_log($cardPropertyValue);
        if(empty($cardPropertyValue) || $cardPropertyValue['card_property_id']<=0 || empty($cardPropertyValue['card_property_id']))
        {
          $userCardLevel = $cardLib->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($cardId);
          $cardPropertyValue = $cardLib->getCardPropertyValue($cardId, $userCardLevel, $cardProperty['card_property_id']);
        }*/
        $userCardLevel = $cardLib->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($cardId);
        $cardPropertyValue = $cardLib->getCardPropertyWithMultipleLevelIdValue($cardId, $opponentCardLevel['level_id'],$userDetails['level_id'], $cardProperty['card_property_id']);



        //print_log($cardPropertyValue);
        $cardLib->insertUserCardProperty(array(
                    'user_id' => $userId,
                    'card_property_id' => $cardProperty['card_property_id'],
                    'user_card_id' => $userCardId,
                    'user_card_property_value' => $cardPropertyValue['card_property_value'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => CONTENT_ACTIVE));
      }	
		
    }
    //$checkPreviousCardList = implode(',', $resCard);
  }
  
  public function getUnlockedCardsListbyCardType($masterStId, $deckId, $rank, $cardType){
  	$sql = "WITH init AS (SELECT mc.card_type,mdc.deck_id,mc.master_stadium_id,mc.master_card_id,
			CASE WHEN mc.master_stadium_id<=:masterStId THEN 'OK' ELSE  'Not OK' END AS stat
			FROM master_deck_card mdc JOIN master_card mc ON 
			mc.master_card_id=mdc.master_card_id WHERE mdc.deck_id=:deckId),
			cnt AS(
			SELECT card_type,stat,COUNT(*) AS nt
			FROM init 
			WHERE stat='Not OK'
			GROUP BY card_type
			ORDER BY COUNT(*)
			),
			ph1 AS (
			SELECT card_type FROM init WHERE stat='Not OK'
			),
			ph2 AS (
			SELECT mc.card_type,mc.master_stadium_id,mc.master_card_id,'OK' AS stat
			#CASE WHEN mc.master_stadium_id<=:masterStId THEN 'OK' ELSE  'Not OK ' END AS stat
			FROM master_card mc
			WHERE  mc.master_stadium_id<=:masterStId AND 
			mc.card_type IN (SELECT card_type FROM ph1) 
			)
			#select * from cnt
			SELECT RANK() OVER(PARTITION BY card_type ORDER BY master_card_id) rank_c ,card_type,master_stadium_id,master_card_id,'OK' AS stat FROM ph2";

    $result = database::doSelect($sql, array('masterStId' => $masterStId,'deckId' => $deckId, 'rank' => $rank,'cardType' => $cardType));
    foreach ($result as $resvalue) {
    	if($resvalue['rank_c']==$rank && $resvalue['card_type']==$cardType){
    		return $resvalue['master_card_id'];
    	}
    }
  }
  /*
  public function getUnlockedCardsListbyCardType($masterStId, $deckId, $rank, $cardType){
    $sql="WITH init AS (SELECT mcrd.card_type, mcrd.master_stadium_id, mcrd.master_card_id
            FROM master_card mcrd 
            WHERE mcrd.master_stadium_id<=:masterStId AND mcrd.master_card_id NOT IN(SELECT master_card_id FROM master_deck_card mdc WHERE mdc.deck_id=:deckId))
            SELECT RANK() OVER(PARTITION BY card_type ORDER BY master_card_id) rank_c ,card_type,master_stadium_id,master_card_id FROM init";
    $result = database::doSelect($sql, array('masterStId' => $masterStId,'deckId' => $deckId, 'rank' => $rank,'cardType' => $cardType));
    foreach ($result as $resvalue) {
      if($resvalue['rank_c']==$rank && $resvalue['card_type']==$cardType){
        return $resvalue['master_card_id'];
      }
    }
  }
  public function getUnlockedCardsList($masterStId, $deckId){
    $sql = "WITH init AS (SELECT mcrd.card_type, mcrd.master_stadium_id, mcrd.master_card_id
            FROM master_card mcrd 
            WHERE mcrd.master_stadium_id<=:masterStId AND mcrd.master_card_id NOT IN(SELECT master_card_id FROM master_deck_card mdc WHERE mdc.deck_id=:deckId))
            SELECT RANK() OVER(PARTITION BY card_type ORDER BY master_card_id) rank_c ,card_type,master_stadium_id,master_card_id FROM init";
    $result = database::doSelect($sql, array('masterStId' => $masterStId,'deckId' => $deckId));
    return $result;
  }*/
  
  public function getUnlockedCardsList($masterStId, $deckId){
  	$sql = "WITH init AS (SELECT mc.card_type,mdc.deck_id,mc.master_stadium_id,mc.master_card_id,
			CASE WHEN mc.master_stadium_id<=:masterStId THEN 'OK' ELSE  'Not OK' END AS stat
			FROM master_deck_card mdc JOIN master_card mc ON 
			mc.master_card_id=mdc.master_card_id WHERE mdc.deck_id=:deckId),
			cnt AS(
			SELECT card_type,stat,COUNT(*) AS nt
			FROM init 
			WHERE stat='Not OK'
			GROUP BY card_type
			ORDER BY COUNT(*)
			),
			ph1 AS (
			SELECT card_type FROM init WHERE stat='Not OK'
			),
			ph2 AS (
			SELECT mc.card_type,mc.master_stadium_id,mc.master_card_id,'OK' AS stat
			#CASE WHEN mc.master_stadium_id<=:masterStId THEN 'OK' ELSE  'Not OK ' END AS stat
			FROM master_card mc
			WHERE  mc.master_stadium_id<=:masterStId AND 
			mc.card_type IN (SELECT card_type FROM ph1) 
			)
			#select * from cnt
			SELECT RANK() OVER(PARTITION BY card_type ORDER BY master_card_id) rank_c ,card_type,master_stadium_id,master_card_id,'OK' AS stat FROM ph2";

    $result = database::doSelect($sql, array('masterStId' => $masterStId,'deckId' => $deckId));
    return $result;
  }
  public function getCval($card, $deckCardList,$userStadiumId, $previouCardId){
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $userLib = autoload::loadLibrary('queryLib', 'user');
  
        //$getCardIds = $cardLib->getCardDetails($userStadiumId, $card['card_type'], $card['card_rarity_type']); 
        /*if(!empty($getCardIds)){
          foreach ($getCardIds as $getCardId) { 
            if(!empty($previouCardId) && count($previouCardId) > 0){
              if(!in_array($getCardId['master_card_id'], $deckCardList)){
                if(!in_array($getCardId['master_card_id'], $previouCardId)){
                  if($getCardId['master_stadium_id']<=$userStadiumId){
                    $cardId = $getCardId['master_card_id'];  
                  }
                }
              }else{*/
    if(!empty($previouCardId) && count($previouCardId) > 0){
      $getCardCTIds = $cardLib->getCardFromCardType($userStadiumId, $card['card_type']);
      if(!empty($getCardCTIds)){
        foreach ($getCardCTIds as $getCardCTId) {
          if(in_array($getCardCTId['master_card_id'], $deckCardList) == false){
            if(in_array($getCardCTId['master_card_id'], $previouCardId) == false){
              if($getCardCTId['master_stadium_id']<=$userStadiumId){
                $cardId = $getCardCTId['master_card_id'];  
              }  
            }
          }}
      }else{
        $getCardMSIds = $cardLib->getCardFromMasterStadiumId($userStadiumId); 
        if(!empty($getCardMSIds)){
          foreach ($getCardMSIds as $getCardMSId) {
            if(in_array($getCardMSId['master_card_id'], $deckCardList) == false){
              if(in_array($getCardMSId['master_card_id'], $previouCardId) == false){
                if($getCardMSId['master_stadium_id'] <= $userStadiumId){
                  $cardId = $getCardMSId['master_card_id'];  
                }
                else{
                  $cardId = $card['master_card_id']; 
                }  
              }
              
            }
          } 
        }
      }
    }
             //}}}
    else{
      /*if(!in_array($getCardId['master_card_id'], $deckCardList)){
        if($userStadiumId <= $getCardId['master_stadium_id']){
          $cardId = $getCardId['master_card_id'];  
        }
      }else{*/
      $getCardCTIds = $cardLib->getCardFromCardType($userStadiumId, $card['card_type']);
      if(!empty($getCardCTIds)){
        foreach ($getCardCTIds as $getCardCTId) {
          if(in_array($getCardCTId['master_card_id'], $deckCardList) == false){
            if($getCardCTId['master_stadium_id']<=$userStadiumId){
              $cardId = $getCardCTId['master_card_id'];  
            }
          }
        } 
      }else{
        $getCardMSIds = $cardLib->getCardFromMasterStadiumId($userStadiumId); 
        if(!empty($getCardMSIds)){
          foreach ($getCardMSIds as $getCardMSId) {
            if(in_array($getCardMSId['master_card_id'], $deckCardList) == false){
             if($getCardMSId['master_stadium_id']<=$userStadiumId){
                $cardId = $getCardMSId['master_card_id'];  
             }
              else{
                $cardId = $card['master_card_id']; 
              }
            }
          } 
        }
      }
    }
        //}}}}}
    return $cardId;
  }
  public function getCardPrevious($userId){
    $sql = "SELECT uc.master_card_id, uc.user_id
            FROM user_card uc
            WHERE uc.user_id=:userId";
    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }
  public function getLevelForProbability($prob, $levelData){
    foreach ($levelData as $data) {
      if($prob >= $data['min_prob'] && $prob <= $data['max_prob'])
        return $data['level'];
    }
    return 1;
  }

  public function getMinMaxDefinedArrayForProbability($rawData){
    $startWithProb = 0;
    $modifiedData = array();
    foreach ($rawData as $data) {
      $temp = array();
      $temp['level'] = $data['card_level'];
      $temp['min_prob'] = $startWithProb;
      $temp['max_prob'] = $startWithProb + $data['level_ratio'];
      $startWithProb = $temp['max_prob'] + 1;
      $modifiedData[] = $temp;
    }
    return $modifiedData;
  }

  public function addNewAi($aiLevel, $aiStadiumId, $relics, $deckId){
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');

    $defaultTowerDetail = $cardLib->getMasterLevelUpXpDetail($aiLevel);

    $accessToken = md5(md5(rand(11111, 555555)).md5(time()));
    $userId = $userLib->insertUser(array(
                'name' => $this->getRandomName(),
                'type' => USER_TYPE_GUEST,
                'is_ai' => CONTENT_ACTIVE,
                'ai_deck_id' => $deckId,
                'is_ai_available' => CONTENT_INACTIVE,
                'ai_game_started_at' => date('Y-m-d H:i:s'),
                'access_token' => $accessToken,
                'master_stadium_id' => $aiStadiumId,
                'relics' => $relics,
                'level_id' => $aiLevel,
                'notification_status' => CONTENT_ACTIVE,
                'god_tower_health' => $defaultTowerDetail['god_tower_health'],
                'stadium_tower_damage' => $defaultTowerDetail['stadium_tower_damage'],
                'god_tower_damage' => $defaultTowerDetail['god_tower_damage'],
                'is_tutorial_completed' => CONTENT_INACTIVE,
                'stadium_tower_health' => $defaultTowerDetail['stadium_tower_health'],
                'is_copper_cube_notification_sent' => CONTENT_ACTIVE,
                'gold' => rand(50, 100),
                'crystal' => rand(10, 50),
                'created_at' => date('Y-m-d H:i:s'),
                'status' => CONTENT_ACTIVE));

    return $userId;
  }

  public function checkForAvailableAi($level, $stadiumId, $relic, $possibleDeck){
    $sql = "SELECT *
            FROM user
            WHERE level_id = :level
            AND master_stadium_id = :stadiumId
            AND is_ai = :active
            AND (is_ai_available =:active OR ai_game_started_at < :lastHalfHour)
            AND ai_deck_id IN ('".$possibleDeck."')
            ORDER BY  user.relics - :relics";

    $lastHalfHour = date('Y-m-d H:i:s', strtotime('-30 minutes'));
    $result = database::doSelect($sql, array('level' => $level, 'stadiumId' => $stadiumId, 'active' => CONTENT_ACTIVE, 'relics' => $relic, 'lastHalfHour' => $lastHalfHour));
    return $result;
  }

  public function getPossibleDecksForUserLevel($level){
    $sql = "SELECT *
            FROM master_level_ai_deck
            WHERE user_level = :level";

    $result = database::doSelect($sql, array('level' => $level));
    return $result;
  }

  public function getDeckLevelAndRatioInfoForDeck($deckId){
    $sql = "SELECT *
            FROM master_ai_deck_info
            WHERE deck_id = :deckId";

    $result = database::doSelect($sql, array('deckId' => $deckId));
    return $result;
  }

  public function getCardListForDeck($deckId){
   /* $sql = "SELECT *
            FROM master_deck_card
            WHERE deck_id = :deckId";
    */ 
    $sql = "SELECT mdc.*, mc.master_stadium_id,mc.card_type,mc.card_rarity_type
            FROM master_deck_card mdc
            LEFT JOIN master_card mc ON mc.master_card_id=mdc.master_card_id
            WHERE deck_id = :deckId";

    $result = database::doSelect($sql, array('deckId' => $deckId));
    return $result;
  }

  public function getCardIdWithDeckAndMasterCard($masterStadiumId, $deckId){
    $sql="
        WITH init AS (SELECT mcrd.card_type, mcrd.master_stadium_id, mcrd.master_card_id
          FROM master_card mcrd 
          WHERE mcrd.master_stadium_id<=:masterStadiumId AND mcrd.is_available=1 AND mcrd.master_card_id NOT IN(SELECT master_card_id FROM master_deck_card mdc WHERE mdc.deck_id=:deckId ORDER BY RAND())),
          init2 AS (SELECT mcrd.card_type, mcrd.master_stadium_id, mcrd.master_card_id
          FROM master_card mcrd 
          WHERE mcrd.master_stadium_id<=:masterStadiumId AND mcrd.is_available=1 AND mcrd.master_card_id IN(SELECT master_card_id FROM master_deck_card mdc WHERE mdc.deck_id=:deckId))
          SELECT RANK() OVER(PARTITION BY card_type ORDER BY master_card_id) rank_c ,card_type,master_stadium_id,master_card_id FROM init2 
          UNION
          SELECT RANK() OVER(PARTITION BY card_type ORDER BY master_card_id) rank_c ,card_type,master_stadium_id,master_card_id FROM init
          #ORDER BY RAND()
          LIMIT 8";
    $result = database::doSelect($sql, array('masterStadiumId'=>$masterStadiumId, 'deckId' => $deckId));
    return $result;
  }

  public function getRandomName(){
    $randLength = rand(4,7);
    $randString = substr(md5(md5(rand(1, 1000)).md5(time())), 0, $randLength );
    $randName = AI_NAMES[array_rand(AI_NAMES)];
    return $randName;
  }
}
