<?php
/**
 * Author : Abhijth Shetty
 * Date   : 06-01-2018
 * Desc   : This is a controller file for roomSaveResult Action
 */
class roomSaveResultAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=room.saveResult", tags={"Rooms"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="room_id", name="room_id", description="The room_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="win_status", name="win_status", description="The win_status specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="opponent_id", name="opponent_id", description="The opponent_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="circlet", name="circlet", description="The circlet specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="opponent_circlet", name="opponent_circlet", description="The opponent_circlet specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false), 
   * @OA\Parameter(parameter="battle_opp_id", name="battle_opp_id", description="The battle_opp_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false), 
   * @OA\Response(response="200", description="Success, Everything worked as expected"),
   * @OA\Response(response="201", description="api_method does not exists"),
   * @OA\Response(response="202", description="The requested version does not exists"),
   * @OA\Response(response="203", description="The requested request method does not exists"),
   * @OA\Response(response="204", description="The auth token is invalid"),
   * @OA\Response(response="205", description="Response code failure"),
   * @OA\Response(response="206", description="paramName should be a Valid email address"),
   * @OA\Response(response="216", description="Invalid Credential, Please try again."),
   * @OA\Response(response="228", description="error"),
   * @OA\Response(response="231", description="Device token is mandatory."),
   * @OA\Response(response="232", description="Custom Error"),
   * @OA\Response(response="245", description="Player is offline"),
   * @OA\Response(response="404", description="Not Found")
   * )
   */
  public function execute()
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    $cubeLib = autoload::loadLibrary('queryLib', 'cube');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    $badgeLib = autoload::loadLibrary('queryLib', 'badge');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $deckLib = autoload::loadLibrary('queryLib', 'deck');
    $inviteLib = autoload::loadLibrary('queryLib', 'invite');
    $questLib = autoload::loadLibrary('queryLib', 'quest');
    date_default_timezone_set("Asia/Kolkata");
    $result = array();
    $cubeBonus = new ArrayObject();
    $responseFormat  = new ArrayObject();
    $slotList = array(1, 2, 3, 4);

    $user = $userLib->getUserDetail($this->userId);
    $uRelics = $user['relics'];
    
    //this return is for friendly battle
    if($this->room_type==2){ 
      $this->setResponse('SUCCESS');
      return $responseFormat;
    }
    if($this->room_type==3){ 
        if($this->winStatus == BATTLE_WON_STATUS){
          $oppBattleStatus = BATTLE_LOST_STATUS;
        }elseif($this->winStatus == BATTLE_DRAW_STATUS){
          $oppBattleStatus = BATTLE_WON_STATUS;
        }else{
          $oppBattleStatus = BATTLE_WON_STATUS;
        }

          $oppUserData = $userLib->getUserDetail($this->battle_opp_id);
          $opponentUser = $userLib->getUserDetail($this->battle_opp_id);
          $oppRelics = $oppUserData['relics'];
          $userDeckLst = $deckLib->getUserDeckDetail($this->userId);
          
          //------------------------------------- deck -----------------------------
          //$userDeckLst = $deckLib->getUserDeckDetail($userId);
          if(empty($userDeckLst)){
            $resultDeck = array();
            $DeskList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE); 
            $deckFLst=array();
            $resultDeck['current_deck_number']=0;
            for($i=0;$i<=3;$i++){
              $deckLst=array();
              $deckLst['deck_id']=$i;
              $j=0;
              if($j<=7){
                $oppdeckList1=array();
                foreach ($DeskList as $dcard) 
                {
                  $cardPropertyInfo2 = $temp2 = array();
                  $temp2['master_card_id'] = $dcard['master_card_id'];
                  $oppdeckList1[] = $temp2;
                  $j++;
                }
              }
              $deckLst['cards']=$oppdeckList1;
              $deckFLst[]=$deckLst;
            }
            $resultDeck['deck_details']= $deckFLst;
            $userDeckLst = json_encode($resultDeck);
            /*$deckLib->insertUserDeck(array(
              'user_id' => $userId,
              'deck_data' => json_encode($resultDeck),
              'created_at' => date('Y-m-d H:i:s'),
              'status' => CONTENT_ACTIVE
            ));*/
          }
      //--------------------------------------------- deck ----------------------------
          $oppDeckLst = $deckLib->getUserDeckDetail($this->battle_opp_id);
          
          //------------------------------------- deck -----------------------------
          //$userDeckLst = $deckLib->getUserDeckDetail($userId);
          if(empty($oppDeckLst)){
            $resultDeck = array();
            $DeskList = $cardLib->getUserCardForActiveDeck($this->battle_opp_id, DECK_ACTIVE); 
            $deckFLst=array();
            $resultDeck['current_deck_number']=0;
            for($i=0;$i<=3;$i++){
              $deckLst=array();
              $deckLst['deck_id']=$i;
              $j=0;
              if($j<=7){
                $oppdeckList1=array();
                foreach ($DeskList as $dcard) 
                {
                  $cardPropertyInfo2 = $temp2 = array();
                  $temp2['master_card_id'] = $dcard['master_card_id'];
                  $oppdeckList1[] = $temp2;
                  $j++;
                }
              }
              $deckLst['cards']=$oppdeckList1;
              $deckFLst[]=$deckLst;
            }
            $resultDeck['deck_details']= $deckFLst;
            $oppDeckLst = json_encode($resultDeck);
            /*$deckLib->insertUserDeck(array(
              'user_id' => $userId,
              'deck_data' => json_encode($resultDeck),
              'created_at' => date('Y-m-d H:i:s'),
              'status' => CONTENT_ACTIVE
            ));*/
          }
      //--------------------------------------------- deck ----------------------------

          $userDeck = $deckLib->getUserDeckDetail($this->userId);
        /*if(!empty($userDeck)) {
          $deckData = json_decode($userDeck['deck_data'],true);
          $deckCards = formatArr($deckData['deck_details'], 'deck_id');
          $data = (array_column($deckCards[$deckData['current_deck_number']]['cards'], 'master_id'));
          $usersDeskList = $cardLib->getUserCardForCurrentDeck($this->userId, DECK_ACTIVE, implode(',',$data)); 
        } else {*/
          $usersDeskList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE); 
        //}

        $oppDeck = $deckLib->getUserDeckDetail($this->battle_opp_id);
       /* if(!empty($oppDeck)) {
          $deckData = json_decode($oppDeck['deck_data'],true);
          $deckCards = formatArr($deckData['deck_details'], 'deck_id');
          $data = (array_column($deckCards[$deckData['current_deck_number']]['cards'], 'master_id'));
          $oppsDeskList = $cardLib->getUserCardForCurrentDeck($this->battle_opp_id, DECK_ACTIVE, implode(',',$data)); 
        } else {*/
          $oppsDeskList = $cardLib->getUserCardForActiveDeck($this->battle_opp_id, DECK_ACTIVE); 
        //}
        //$usersDeskList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE); 
        //$oppsDeskList = $cardLib->getUserCardForActiveDeck($this->battle_opp_id, DECK_ACTIVE); 
        foreach ($usersDeskList as $card)
        {
          $cardPropertyInfo = $temp = array();
          $temp['user_card_id'] = $card['user_card_id'];
          $temp['master_card_id'] = $card['master_card_id'];
          $temp['title'] = $card['title'];
          $temp['card_type'] = $card['card_type'];
          $temp['card_type_message'] = ($card['card_type'] == CARD_TYPE_TROOP)?"Troop":(($card['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
          $temp['card_rarity_type'] = $card['card_rarity_type'];
          $temp['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($card['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
          $temp['is_deck_message'] = ($card['is_deck'] == CONTENT_ACTIVE)?"in deck":"not in deck";
          $temp['is_deck'] = $card['is_deck'];
          $temp['card_level'] = $card['level_id'];
          //$temp['card_description'] = $card['card_description'];
          $usrdeckList[] = $temp;
        }
        foreach ($oppsDeskList as $oppcard) 
        {
          $cardPropertyInfo2 = $temp2 = array();
          $temp2['user_card_id'] = $oppcard['user_card_id'];
          $temp2['master_card_id'] = $oppcard['master_card_id'];
          $temp2['title'] = $oppcard['title'];
          $temp2['card_type'] = $oppcard['card_type'];
          $temp2['card_type_message'] = ($oppcard['card_type'] == CARD_TYPE_TROOP)?"Troop":(($oppcard['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
          $temp2['card_rarity_type'] = $oppcard['card_rarity_type'];
          $temp2['rarity_type_message'] = ($oppcard['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($oppcard['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($oppcard['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
          $temp2['is_deck_message'] = ($oppcard['is_deck'] == CONTENT_ACTIVE)?"in deck":"not in deck";
          $temp2['is_deck'] = $oppcard['is_deck'];
          $temp2['card_level'] = $oppcard['level_id'];
          //$temp2['card_description'] = $oppcard['card_description'];
          $oppdeckList[] = $temp2;
        } 
        $kingdomBattleData = $kingdomLib->getKingdomBattleByStateMsgType($this->userId, 3, 5);
        if(empty($kingdomBattleData)){
          $kingdomBattleData = $kingdomLib->getKingdomBattleByStateMsgTypeByOppId($this->userId, $this->battle_opp_id, 3, 5);
        }
        $deletemsgId = $kingdomLib->deleteKingdomRequestedMsgListMsgType($this->userId, 3, 5);
        if(empty($deletemsgId)){
          $deletemsgId = $kingdomLib->deleteKingdomRequestedMsgListMsgTypeByRoom($this->userId,$this->roomId, 3, 5);
          if(empty($deletemsgId)){
            $deletemsgId = $kingdomLib->deleteKingdomRequestedMsgListMsgTypeByOpp($this->userId,$this->battle_opp_id, 3, 5);
          }
        }
        $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($this->userId);
        if(empty($frndlyBattleDetails)){
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByOppId($this->battle_opp_id);
        }
        $msg_delete_id=$kingdomBattleData['km_id'];
        $isMsg_RoomId_check = $userLib->getCheckRoomIdInmsgLst($this->roomId);
        if(!empty($deletemsgId) || empty($isMsg_RoomId_check)){  //&& empty($isMsg_RoomId_check)
          $msgId = $kingdomLib->insertKingdomMsg(array(
            'kingdom_id' => $user['kingdom_id'],
            'sent_by' => $this->userId,
            'received_by' => $this->battle_opp_id,
            'msg_type' => 3,
            'chat_type' => 2,
            'battle_type' => 1,
            'room_id' => $this->roomId,
            'battle_state' => 3,
            'msg_delete_id' => $msg_delete_id, 
            'created_at' => date('Y-m-d H:i:s')
          ));
        }/*else{
          $msgId = $kingdomLib->insertKingdomMsg(array(
            'kingdom_id' => $user['kingdom_id'],
            'sent_by' => $this->userId,
            'received_by' => $this->battle_opp_id,
            'msg_type' => 3,
            'chat_type' => 2,
            'battle_type' => 1,
            'room_id' => $this->roomId,
            'battle_state' => 3,
            'msg_delete_id' => "", 
            'created_at' => date('Y-m-d H:i:s')
          ));
        }*/
        
        
        //$is_RoomId_check = $userLib->getCheckRoomIdInFriendlyBattleHistoryList($this->roomId);
        if($this->userId != 0 && $this->battle_opp_id != 0){
          //&& empty($is_RoomId_check )
            $battleId = $userLib->insertFriendlyBattleHistory(array(
              'user_id' => $this->userId, 
              'opponent_id' => $this->battle_opp_id,
              'room_id' => $this->roomId,
              'user_circlet' => $this->circlet,
              'opponent_circlet' => $this->opponent_circlet,
              'user_trophies' => $uRelics,
              'opponent_trophies' => $oppRelics,
              'user_deck' => $userDeckLst['deck_data'],
              'opponent_deck' => $oppDeckLst['deck_data'],
              'user_winstatus' => $this->winStatus,
              'opponent_winstatus' => $oppBattleStatus,
              'user_stadium' => $user['master_stadium_id'],
              'opp_stadium' => $oppUserData['master_stadium_id'], 
              'km_id' => $msgId,
              'created_at' => date('Y-m-d H:i:s'),
              'userDeckLst'=> json_encode($usrdeckList),
              'oppDeckLst'=> json_encode($oppdeckList),
              'created_by' => $this->userId));
            
            //$opponentUser = $roomLib->getOpponentRoomUserForRoomAndUser($this->userId, $this->roomId);
            // if(!empty($opponentUser) && $opponentUser['is_ai'] != CONTENT_ACTIVE){
           /* if(empty($isMsg_RoomId_check)){
              $battleOpponentId = $userLib->insertFriendlyBattleHistory(array(
                'user_id' => $this->battle_opp_id,
                'opponent_id' => $this->userId,
                'room_id' => $this->roomId,
                'user_circlet' => $this->opponent_circlet,
                'opponent_circlet' => $this->circlet,
                'user_trophies' => $oppRelics,
                'opponent_trophies' => $uRelics,
                'user_deck' => $oppDeckLst['deck_data'],
                'opponent_deck' => $userDeckLst['deck_data'],
                'user_winstatus' => $oppBattleStatus,
                'opponent_winstatus' => $this->winStatus,
                'user_stadium' => $oppUserData['master_stadium_id'],
                'opp_stadium' => $user['master_stadium_id'], 
                'userDeckLst'=> json_encode($usrdeckList),
                'oppDeckLst'=> json_encode($oppdeckList),
                'km_id' => $msgId,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->userId));  
            }   */
        }
        $responseFormat = array('room_type' => $this->room_type);
        $this->setResponse('SUCCESS');
        return $responseFormat;
    }
    if($this->winStatus != BATTLE_DRAW_STATUS && $this->winStatus != BATTLE_WON_STATUS && $this->winStatus != BATTLE_LOST_STATUS)
    {
      $this->setResponse('CUSTOM_ERROR', array('error'=>'invalid option'));
      return new ArrayObject();
    }

    if($this->winStatus > -1)
    {
      //$matchStatusReward = $rewardLib->getMasterMatchStatusRewardForStadium($this->winStatus, $user['master_stadium_id']);
      $default_circlet=0;
      $total_circlet=3;
      if($this->winStatus==1){
        $destroyed_tower=$this->circlet;
      }elseif($this->winStatus==2){
        $destroyed_tower=$this->opponent_circlet;
      }elseif($this->winStatus==3){
        $destroyed_tower=$this->circlet;
      }

     $matchStatusReward = $rewardLib->getMasterMatchStatusRewardForStadiumByTower($this->winStatus, $destroyed_tower, $user['master_stadium_id']);
      if(empty($matchStatusReward) || $matchStatusReward == ""){
        $mSId=$rewardLib->getMaxStadiumIdMasterMatchStatusRewardForStadium();
        $maxStadiumId=$mSId['master_stadium_id'];
        if(empty($maxStadiumId) || $maxStadiumId==""){
          $maxStadiumId=5;
        }
        //$matchStatusReward = $rewardLib->getMasterMatchStatusRewardForStadium($this->winStatus, $maxStadiumId);
        $matchStatusReward = $rewardLib->getMasterMatchStatusRewardForStadiumByTower($this->winStatus, $destroyed_tower, $maxStadiumId);
      }
      $roomUserActive = $roomLib->getWaitingRoomActiveForRoom($this->userId, $this->roomId);

      if(!empty($roomUserActive))
      {
        if($this->circlet != "")
        {
          $roomParamList['circlet'] = $this->circlet;
          $userParamList['circlet'] = $result['circlet'] = ($user['circlet'] + $this->circlet);
        }


        $roomParamList['win_status'] = $this->winStatus;
        $relics = ($user['relics'] + $matchStatusReward['relics']);
        $userParamList['total_wins'] = ($this->winStatus == BATTLE_WON_STATUS)? $user['total_wins']+1:$user['total_wins'];
        $userParamList['total_match'] = $user['total_match']+1;
        $userParamList['relics'] = ($relics <= MIN_RELICS_COUNT)? MIN_RELICS_COUNT:$relics;//Set minimum relics to = 0.
        $userParamList['xp'] = $user['xp'] + $matchStatusReward['xp'];
        $userParamList['gold'] = $user['gold'] + $matchStatusReward['gold'];

        //Based on relics count update the player stadium.
        $masterStadium = $masterLib->getStadiumIdBasedOnRelics($userParamList['relics']);
        if(empty($masterStadium) || $masterStadium['master_stadium_id']>=10){
          $userParamList['master_stadium_id'] =10;
        }else{
          if($masterStadium['master_stadium_id']>$user['max_stadium_id']){
            $userParamList['max_stadium_id'] = $masterStadium['master_stadium_id'];
            $userParamList['stadium_level_up'] = 1;
            
            //----------------------------- quest reached leve 5 stadium  -----------------------------------
          $qUserStadiumv = $questLib->getQuestUserStadium5Reward($this->userId);
          $user = $userLib->getUserDetail($this->userId);
            if($qUserStadiumv['slide_count']>=$qUserStadiumv['slide_maxvalue']){
              $questData= $questLib->getBattleQuestData(7,$this->userId);
              if(empty($questData)){
                if($user['master_stadium_id']<=5){
                  $questLib->insertMasterQuestInventory(array(
                    'quest_id' => 7,
                    'time' => date('Y-m-d H:i:s'),
                    'user_id' => $this->userId,
                    'status' => CONTENT_ACTIVE,
                    'slide_count'=>!empty($user['master_stadium_id'])?$user['master_stadium_id']:1,
                    'created_at' => date('Y-m-d H:i:s')));
                }elseif($user['master_stadium_id']>=5){
                  $questLib->insertMasterQuestInventory(array(
                    'quest_id' => 7,
                    'time' => date('Y-m-d H:i:s'),
                    'user_id' => $this->userId,
                    'status' => CONTENT_ACTIVE,
                    'slide_count'=>$qUserStadiumv['slide_maxvalue'],
                    'created_at' => date('Y-m-d H:i:s')));
                }
                
              }else{
                $qUserStadiumv = $questLib->getQuestUserStadium5Reward($this->userId);
                $user = $userLib->getUserDetail($this->userId);
                $questData= $questLib->getBattleQuestData(7,$this->userId);
                if($qUserStadiumv['slide_count']!=$qUserStadiumv['slide_maxvalue']){  
                  if($user['master_stadium_id']<=5){
                    $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('slide_count' => $user['master_stadium_id']));
                  }elseif($user['master_stadium_id']>=5){
                    $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('slide_count' => $qUserStadiumv['slide_maxvalue']));
                  }else{
                    $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('slide_count' => $questData['slide_count']+1));
                  } 
                } 
              } 
            }
          //$qv = $questLib->getQuestUserStadium5Reward($this->userId);
          // -------------------------------- quest reached leve 5 stadium  --------------------------
            
          }else{
            $userParamList['max_stadium_id'] = $user['max_stadium_id'];
          }
          $userParamList['master_stadium_id'] =  $masterStadium['master_stadium_id'];
        }
        
        // if($relics > $user['relics']) {
        //   $masterStadium = $masterLib->getStadiumIdBasedOnRelics($userParamList['relics']);
        //   $userParamList['master_stadium_id'] =  $masterStadium['master_stadium_id'];
        // } else {
        //   //destroy stadium based on relics deduction
        //   $destroyedStadium = $masterLib->getDestroyedStadiumIdBasedOnRelics($userParamList['relics']);
        //   $userParamList['master_stadium_id'] = $destroyedStadium['master_stadium_id'] - 1;
        //   ($userParamList['master_stadium_id'] <= DEFAULT_STADIUM) ? $userParamList['master_stadium_id'] = $user['master_stadium_id'] : false;
        // }
        //on stadium level up unlock the cards
        if($user['master_stadium_id'] < $userParamList['master_stadium_id']) {
          $cardLib->cardUnlock($this->userId, $userParamList['master_stadium_id']);
        }

        //if draw or lost update win streak to 0
        if($this->winStatus == BATTLE_WON_STATUS )
        {
          $roomParamList['win_streak'] = 0;
        }
        // Based on probabilty getting the cube
        if($this->winStatus == BATTLE_WON_STATUS )
        {
          //update win streak for user
          $userPreviousRoom = $roomLib->getPreviousWaitingRoomDetail($this->userId, $this->roomId);
          if(!empty($userPreviousRoom) && $userPreviousRoom['win_status'] == BATTLE_WON_STATUS)
          {
            $roomParamList['win_streak'] = $userPreviousRoom['win_streak'] + 1;
          } else {
            $roomParamList['win_streak'] = 1;
          }
          //copper cube is the free cube
    /*      do{
            if($user['master_stadium_id'] < 3){
                $randomCube=rand(1 , 3 );
            }
            else{
                $randomCube=rand(1 , 5 );
            }
          }while($randomCube==3);
*/
          $randomCubeProbability = rand(1 , 100);

          //check user played first match or Not, if first match give one cube
          $userMatch = $roomLib->getRoomPlayedListForUser($this->userId);
/*
          $cubeProbability = $cubeLib->getCubeProbabilityDetailForStadium($randomCube, $user['master_stadium_id'], $randomCubeProbability);
          if(empty($cubeProbability)){
            do{
              if($user['master_stadium_id'] < 3){
                $randomCube=rand(1 , 3 );
              }
              else{
                  $randomCube=rand(1 , 5 );
              }
            }while($randomCube==4);
            $randomCubeProbability = rand(1 , 100);
          }*/
          /*$arr1= array();
          $arr = $roomLib->getPercentageofCubewithMasterId($user['master_stadium_id']);
          foreach ($arr as $key) {
            $arr1[$key['cube_id']] => $key['percentage'];
          }
          $arr2[] = $arr1; */
          //$probabilities = array();
         /* $probabilities = array(
                                  1 => 70,
                                  2 => 20,
                                  3 => 10
                              );*/
          /*$probabilities = array($arr2);*/
          //$arr = $roomLib->getPercentageofCubewithMasterId($user['master_stadium_id']);
          $cubeDetails = $roomLib->getCubeSequenceMaxPosDetails($this->userId, $user['seq_id']);
         /* foreach ($cubeDetails as $cubeKey) {
            $cubeVal=$cubeKey['seq_pos_id'];
          }*/
          $cubeVal=$cubeDetails['seq_pos_id'];
          if(($cubeVal+1) > 240){

            $userLib->updateUser($this->userId, array('seq_id' => rand(1,10)));
            $user = $userLib->getUserDetail($this->userId);

            $cubeDetails = $roomLib->getCubeSequenceMaxPosDetails($this->userId, $user['seq_id']);
           /* foreach ($cubeDetails as $cubeKey) {
              $cubeVal=$cubeKey['seq_pos_id'];
            } */
            $cubeVal=$cubeDetails['seq_pos_id'];
          }
          if(empty($cubeVal)){
            $cubeVal=0;
          }
          $cubeIdVal = $roomLib->getSequenceCubeId($user['seq_id'], $cubeVal+1);
          /*foreach ($cubeIdVal as $cubeIdKey) {
            $cId=$cubeIdKey['cube_id'];
          }*/
          $cId=$cubeIdVal['cube_id'];

          /*$probabilities= array();
          $arr = $roomLib->getPercentageofCubewithMasterId($user['master_stadium_id']);
          foreach ($arr as $key) {
            $arr1[] = $key['cube_id']." => ".$key['percentage'];
          }
          $probabilities = $arr1;
          $random = array();
          foreach($probabilities as $key => $value) {
              for($i = 0; $i < $value; $i++) {
                  $random[] = $key;
              }
          }
          shuffle($random);
          $randomCube = $random[0];*/
          if($user['master_stadium_id'] <= 2 && $cId < 4){
            $randomCube = $cId;  
          }elseif ($user['master_stadium_id'] ==3 && $cId < 5) {
            $randomCube = $cId;  
          }elseif ($user['master_stadium_id'] > 3 && $cId <= 5) {
            $randomCube = $cId;  
          }else{
            $randomCube = 1;
          }
          

          if(count($userMatch) == 1)
          { 
            //$cubeProbability = $cubeLib->getRandomCubeDetailForStadium($randomCube, $user['master_stadium_id']);
            if(1)
            {
              $cubeBonus['cube_id'] = $roomParamList['cube_id'] = $randomCube;
              $cubeBonus['seq_id']=$user['seq_id'];
              $cubeBonus['seq_pos_id']= $cubeVal+1;
              $userRewardList = $cubeLib->CheckEligibilityOfCubeReward($this->userId);

              if(count($userRewardList) < MIN_CUBE_REWARD)
              {
                foreach($userRewardList as $reward){
                  $slotsFilled[] = $reward['slot_id'];
                }
              }
              $cubeBonus['is_lapsed'] = (count($userRewardList)>= MIN_CUBE_REWARD)?true:false;
            }
          } else {
            $cubeProbability = $cubeLib->getCubeProbabilityDetailForStadium($randomCube, $user['master_stadium_id'], $randomCubeProbability);

            if(!empty($cubeProbability))
            //if(1)
            {
              $cubeBonus['cube_id'] = $roomParamList['cube_id'] = $randomCube;
              $cubeBonus['seq_id']=$user['seq_id'];
              $cubeBonus['seq_pos_id']= $cubeVal+1;
              $userRewardList = $cubeLib->CheckEligibilityOfCubeReward($this->userId);

              if(count($userRewardList) < MIN_CUBE_REWARD)
              {
                foreach($userRewardList as $reward){
                  $slotsFilled[] = $reward['slot_id'];
                }
              }
              $cubeBonus['is_lapsed'] = (count($userRewardList)>= MIN_CUBE_REWARD)?true:false;
            }else{
              $cubeBonus['cube_id'] = $roomParamList['cube_id'] = $randomCube;
              $cubeBonus['seq_id']=$user['seq_id'];
              $cubeBonus['seq_pos_id']= $cubeVal+1;
              $userRewardList = $cubeLib->CheckEligibilityOfCubeReward($this->userId);

              if(count($userRewardList) < MIN_CUBE_REWARD)
              {
                foreach($userRewardList as $reward){
                  $slotsFilled[] = $reward['slot_id'];
                }
              }
              $cubeBonus['is_lapsed'] = (count($userRewardList)>= MIN_CUBE_REWARD)?true:false;
            }

          }
        }

        // opponent Quits the match in between.
        if($this->opponentId > -1)
        {
          $opponentUser = $userLib->getUserDetail($this->opponentId);
          $oppRelics = $opponentUser['relics'];
          $opponentMatchStatusReward = $rewardLib->getMasterMatchStatusRewardForStadium(BATTLE_LOST_STATUS, $opponentUser['master_stadium_id']);

          $opponentParamList['total_wins'] = $opponentUser['total_wins'];
          $opponentRelicsCount = $opponentUser['relics']+$opponentMatchStatusReward['relics'];
          $opponentParamList['relics'] = ($opponentRelicsCount <= MIN_RELICS_COUNT)? MIN_RELICS_COUNT:$opponentRelicsCount;//Set minimum relics to = 0.;
          $opponentParamList['xp'] = $opponentUser['xp'] + $opponentMatchStatusReward['xp'];
          $opponentParamList['gold'] = $opponentUser['gold'] + $opponentMatchStatusReward['gold'];
          $masterStadium = $masterLib->getStadiumIdBasedOnRelics($opponentParamList['relics']);
          $opponentParamList['master_stadium_id'] =  $masterStadium['master_stadium_id'];

          //Update Waiting room and user detail for opponent who quit the match.
          $roomLib->updateWaitingRoomForPlayerResult($this->roomId, $this->opponentId, array('win_status'=> BATTLE_LOST_STATUS));
          $roomLib->updateWaitingRoomStatus($this->roomId, $this->opponentId, array('status'=> CONTENT_CLOSED));
          $userLib->updateUser($this->opponentId, $opponentParamList);
        }

        //Update Waiting room and user detail.
        $roomLib->updateWaitingRoomForPlayerResult($this->roomId, $this->userId, $roomParamList);
        $roomLib->updateWaitingRoomStatus($this->roomId, $this->userId, array('status'=> CONTENT_CLOSED));
        $userLib->updateUser($this->userId, $userParamList);
        $user = $userLib->getUserDetail($this->userId);
       // print_log($user);
        //If player was playing with the AI, upated AI waiting room status, win status.
        $opponentUser = $roomLib->getOpponentRoomUserForRoomAndUser($this->userId, $this->roomId);

        if(!empty($opponentUser) && $opponentUser['is_ai'] == CONTENT_ACTIVE){
          if($this->winStatus == BATTLE_WON_STATUS)
            $aiBattleStatus = BATTLE_LOST_STATUS;
          else
            $aiBattleStatus = BATTLE_WON_STATUS;

          $roomLib->updateWaitingRoomStatus($this->roomId, $opponentUser['user_id'], array('status'=> CONTENT_CLOSED, 'win_status' => $aiBattleStatus));
          $userLib->updateUser($opponentUser['user_id'], array('is_ai_available' => CONTENT_ACTIVE));
        }
      }
    } 

    //if user rewarded with a cube then add to user_reward
    if(!($cubeBonus['is_lapsed']) && !empty($cubeBonus['cube_id']))
    {
      $cube = $userLib->getMasterCubeRewardForStadium($cubeBonus['cube_id'], $user['master_stadium_id']);
      $freeSlot = (array_diff($slotList, $slotsFilled));

      $userLib->insertUserReward(array(
                  'user_id' => $this->userId,
                  'seq_id' => $cubeBonus['seq_id'],
                  'seq_pos_id' => $cubeBonus['seq_pos_id'],
                  'cube_id' => $cubeBonus['cube_id'],
                  'slot_id' => empty($freeSlot)?1:array_pop(array_reverse($freeSlot)),
                  'master_stadium_id' => $user['master_stadium_id'],
                  'created_at' => date('Y-m-d H:i:s'),
                  'status' => CUBE_ACTIVE));

    }

    //Check User eligibity for the Bronze reward
    // $circlet_count = $rewardLib->checkEligibilityOfBronzeReward($this->userId, $user['master_stadium_id']);

    //Check User Achievement
    //$achieved = $achievementLib->checkUserAchievement($this->userId); // this scenario is closed bcoz we are fetching this from other source

    //levelup the user
    $isLevelIncreased =  $userLib->checkForUserLevelUp($this->userId);

    //fprovide the badge based on relics count to user
    $isBadgeGiven =  $badgeLib->checkUserBadge($this->userId);
    $latestBadge = $badgeLib->getUserLatestBadge($this->userId);
    
    $questData= $questLib->getBattleQuestData(2,$this->userId);
    if(empty($questData)){
      $questLib->insertMasterQuestInventory(array(
        'quest_id' => 2,
        'time' => date('Y-m-d H:i:s'),
        'user_id' => $this->userId,
        'status' => CONTENT_ACTIVE,
        'win_status' => $this->winStatus,
        'room_id' => $this->roomId,
        'win_count' => ($this->winStatus == BATTLE_WON_STATUS)?1:0,
        'match_count'=>1,
        'created_at' => date('Y-m-d H:i:s')));
    }else{
      if($this->winStatus == BATTLE_WON_STATUS){
        $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('win_count'=> $questData['win_count']+1, 'match_count' => $questData['match_count']+1));
      }else{
        $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('match_count' => $questData['match_count']+1));
      } 
    }
    $qv100 = $questLib->getQuestPatBattle100Reward($this->userId, $this->androidVerId, $this->iosVerId);
    $qv200 = $questLib->getQuestPlayBattle200Reward($this->userId, $this->androidVerId, $this->iosVerId);
    $qv500 = $questLib->getQuestPlayBattle500Reward($this->userId, $this->androidVerId, $this->iosVerId);
    if($qv100['match_count']>=$qv100['slide_maxvalue'] || !empty($qv200['match_count'])){
      $questData= $questLib->getBattleQuestData(10,$this->userId);
      if(empty($questData)){
        $questLib->insertMasterQuestInventory(array(
          'quest_id' => 10,
          'time' => date('Y-m-d H:i:s'),
          'user_id' => $this->userId,
          'status' => CONTENT_ACTIVE,
          'win_status' => $this->winStatus,
          'room_id' => $this->roomId,
          'win_count' => ($this->winStatus == BATTLE_WON_STATUS)?1:0,
          'match_count'=>1,
          'created_at' => date('Y-m-d H:i:s')));
      }else{
        if($this->winStatus == BATTLE_WON_STATUS){
          $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('win_count'=> $questData['win_count']+1, 'match_count' => $questData['match_count']+1));
        }else{
          $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('match_count' => $questData['match_count']+1));
        } 
      }
    }
    if($qv200['match_count']>=$qv200['slide_maxvalue'] || !empty($qv500['match_count'])){
      $questData= $questLib->getBattleQuestData(11,$this->userId);
      if(empty($questData)){
        $questLib->insertMasterQuestInventory(array(
          'quest_id' => 11,
          'time' => date('Y-m-d H:i:s'),
          'user_id' => $this->userId,
          'status' => CONTENT_ACTIVE,
          'win_status' => $this->winStatus,
          'room_id' => $this->roomId,
          'win_count' => ($this->winStatus == BATTLE_WON_STATUS)?1:0,
          'match_count'=>1,
          'created_at' => date('Y-m-d H:i:s')));
      }else{
        if($this->winStatus == BATTLE_WON_STATUS){
          $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('win_count'=> $questData['win_count']+1, 'match_count' => $questData['match_count']+1));
        }else{
          $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('match_count' => $questData['match_count']+1));
        } 
      }
    }

    /*$quest_klist = [2,10,11];  // quest_id's list 
    foreach ($quest_klist as $quest_kval)
    {
      $questData= $questLib->getBattleQuestData($quest_kval,$this->userId);
      if(empty($questData)){
        $questLib->insertMasterQuestInventory(array(
          'quest_id' => $quest_kval,
          'time' => date('Y-m-d H:i:s'),
          'user_id' => $this->userId,
          'status' => CONTENT_ACTIVE,
          'win_status' => $this->winStatus,
          'room_id' => $this->roomId,
          'win_count' => ($this->winStatus == BATTLE_WON_STATUS)?1:0,
          'match_count'=>1,
          'created_at' => date('Y-m-d H:i:s')));
      }else{
        if($this->winStatus == BATTLE_WON_STATUS){
          $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('win_count'=> $questData['win_count']+1, 'match_count' => $questData['match_count']+1));
        }else{
          $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('match_count' => $questData['match_count']+1));
        } 
      }
    }*/
    $win_rate = (((empty($userParamList['total_wins'])?$user['total_wins']:$userParamList['total_wins'])/(empty($userParamList['total_match'])?$user['total_match']:$userParamList['total_match']))*100);
    

    //$userDeckLst = $deckLib->getUserDeckDetail($this->userId);
    //$oppDeckLst = $deckLib->getUserDeckDetail($this->battle_opp_id);
    $userDeckLst = $deckLib->getUserDeckDetail($this->userId);
          
    //------------------------------------- deck -----------------------------
    //$userDeckLst = $deckLib->getUserDeckDetail($userId);
    if(empty($userDeckLst)){
      $resultDeck = array();
      $DeskList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE); 
      $deckFLst=array();
      $resultDeck['current_deck_number']=0;
      for($i=0;$i<=3;$i++){
        $deckLst=array();
        $deckLst['deck_id']=$i;
        $j=0;
        if($j<=7){
          $oppdeckList1=array();
          foreach ($DeskList as $dcard) 
          {
            $cardPropertyInfo2 = $temp2 = array();
            $temp2['master_card_id'] = $dcard['master_card_id'];
            $oppdeckList1[] = $temp2;
            $j++;
          }
        }
        $deckLst['cards']=$oppdeckList1;
        $deckFLst[]=$deckLst;
      }
      $resultDeck['deck_details']= $deckFLst;
      $userDeckLst = json_encode($resultDeck);
    }
//--------------------------------------------- deck ----------------------------
    $oppDeckLst = $deckLib->getUserDeckDetail($this->battle_opp_id);
    
    //------------------------------------- deck -----------------------------
     if(empty($oppDeckLst)){
      $resultDeck = array();
      $DeskList = $cardLib->getUserCardForActiveDeck($this->battle_opp_id, DECK_ACTIVE); 
      $deckFLst=array();
      $resultDeck['current_deck_number']=0;
      for($i=0;$i<=3;$i++){
        $deckLst=array();
        $deckLst['deck_id']=$i;
        $j=0;
        if($j<=7){
          $oppdeckList1=array();
          foreach ($DeskList as $dcard) 
          {
            $cardPropertyInfo2 = $temp2 = array();
            $temp2['master_card_id'] = $dcard['master_card_id'];
            $oppdeckList1[] = $temp2;
            $j++;
          }
        }
        $deckLst['cards']=$oppdeckList1;
        $deckFLst[]=$deckLst;
      }
      $resultDeck['deck_details']= $deckFLst;
      $oppDeckLst = json_encode($resultDeck);

    }
//--------------------------------------------- deck ----------------------------



    $oppUserData = $userLib->getUserDetail($this->battle_opp_id);
    if(empty($oppRelics)){
      $oppRelics=$oppUserData['relics'];
    }
    $userCirclets = (($this->winStatus == BATTLE_DRAW_STATUS || empty($roomUserActive))?$user['relics']:$userParamList['relics']);
    if($this->winStatus == BATTLE_WON_STATUS)
      $oppBattleStatus = BATTLE_LOST_STATUS;
    elseif($this->winStatus == BATTLE_DRAW_STATUS)
      $oppBattleStatus = BATTLE_WON_STATUS;
    else
      $oppBattleStatus = BATTLE_WON_STATUS;
    /*$roomPlayersLst = $roomLib->getPlayersForRoomId($this->roomId);
    $usersDeskList = $roomLib->matchingPlayerDetails($roomPlayersLst);*/
    $userDeck = $deckLib->getUserDeckDetail($this->userId);
    if(!empty($userDeck)) {
      $deckData = json_decode($userDeck['deck_data'],true);
      $deckCards = formatArr($deckData['deck_details'], 'deck_id');
      $data = (array_column($deckCards[$deckData['current_deck_number']]['cards'], 'master_id'));
      $usersDeskList = $cardLib->getUserCardForCurrentDeck($this->userId, DECK_ACTIVE, implode(',',$data)); 
    } else {
      $usersDeskList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE); 
    }

    $oppDeck = $deckLib->getUserDeckDetail($this->battle_opp_id);
    if(!empty($oppDeck)) {
      $deckData = json_decode($oppDeck['deck_data'],true);
      $deckCards = formatArr($deckData['deck_details'], 'deck_id');
      $data = (array_column($deckCards[$deckData['current_deck_number']]['cards'], 'master_id'));
      $oppsDeskList = $cardLib->getUserCardForCurrentDeck($this->battle_opp_id, DECK_ACTIVE, implode(',',$data)); 
    } else {
      $oppsDeskList = $cardLib->getUserCardForActiveDeck($this->battle_opp_id, DECK_ACTIVE); 
    }
    //$usersDeskList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE); 
    //$oppsDeskList = $cardLib->getUserCardForActiveDeck($this->battle_opp_id, DECK_ACTIVE); 
    foreach ($usersDeskList as $card)
    {
      $cardPropertyInfo = $temp = array();
      $temp['user_card_id'] = $card['user_card_id'];
      $temp['master_card_id'] = $card['master_card_id'];
      $temp['title'] = $card['title'];
      $temp['card_type'] = $card['card_type'];
      $temp['card_type_message'] = ($card['card_type'] == CARD_TYPE_TROOP)?"Troop":(($card['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
      $temp['card_rarity_type'] = $card['card_rarity_type'];
      $temp['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($card['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
      $temp['is_deck_message'] = ($card['is_deck'] == CONTENT_ACTIVE)?"in deck":"not in deck";
      $temp['is_deck'] = $card['is_deck'];
      $temp['card_level'] = $card['level_id'];
      //$temp['card_description'] = $card['card_description'];
      $usrdeckList[] = $temp;
    }
    foreach ($oppsDeskList as $oppcard) 
    {
      $cardPropertyInfo2 = $temp2 = array();
      $temp2['user_card_id'] = $oppcard['user_card_id'];
      $temp2['master_card_id'] = $oppcard['master_card_id'];
      $temp2['title'] = $oppcard['title'];
      $temp2['card_type'] = $oppcard['card_type'];
      $temp2['card_type_message'] = ($oppcard['card_type'] == CARD_TYPE_TROOP)?"Troop":(($oppcard['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
      $temp2['card_rarity_type'] = $oppcard['card_rarity_type'];
      $temp2['rarity_type_message'] = ($oppcard['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($oppcard['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($oppcard['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
      $temp2['is_deck_message'] = ($oppcard['is_deck'] == CONTENT_ACTIVE)?"in deck":"not in deck";
      $temp2['is_deck'] = $oppcard['is_deck'];
      $temp2['card_level'] = $oppcard['level_id'];
      //$temp2['card_description'] = $oppcard['card_description'];
      $oppdeckList[] = $temp2;
    } 
    $battleId = $userLib->insertBattleHistory(array(
                    'user_id' => $this->userId, 
                    'opponent_id' => $this->battle_opp_id,
                    'room_id' => $this->roomId,
                    'user_circlet' => $this->circlet,
                    'opponent_circlet' => $this->opponent_circlet,
                    'user_trophies' => $uRelics,
                    'opponent_trophies' => $oppRelics,
                    'user_deck' => $userDeckLst['deck_data'],
                    'opponent_deck' => $oppDeckLst['deck_data'],
                    'user_winstatus' => $this->winStatus,
                    'opponent_winstatus' => $oppBattleStatus,
                    'user_stadium' => $user['master_stadium_id'],
                    'opp_stadium' => $oppUserData['master_stadium_id'], 
                    'created_at' => date('Y-m-d H:i:s'),
                    'userDeckLst'=> json_encode($usrdeckList),
                    'oppDeckLst'=> json_encode($oppdeckList),
                    'created_by' => $this->userId));
    if(!empty($opponentUser) && $opponentUser['is_ai'] != CONTENT_ACTIVE){
      $battleOpponentId = $userLib->insertBattleHistory(array(
                            'user_id' => $this->battle_opp_id,
                            'opponent_id' => $this->userId,
                            'room_id' => $this->roomId,
                            'user_circlet' => $this->opponent_circlet,
                            'opponent_circlet' => $this->circlet,
                            'user_trophies' => $oppRelics,
                            'opponent_trophies' => $uRelics,
                            'user_deck' => $oppDeckLst['deck_data'],
                            'opponent_deck' => $userDeckLst['deck_data'],
                            'user_winstatus' => $oppBattleStatus,
                            'opponent_winstatus' => $this->winStatus,
                            'user_stadium' => $oppUserData['master_stadium_id'],
                            'opp_stadium' => $user['master_stadium_id'], 
                            'userDeckLst'=> json_encode($usrdeckList),
                            'oppDeckLst'=> json_encode($oppdeckList),
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $this->userId));    
    }
    
    print_log("xp::".$user['xp']);
    print_log("==================================================");
    $responseFormat = array('win_status' => $this->winStatus,
                        'total_wins' => (empty($userParamList['total_wins'])?$user['total_wins']:$userParamList['total_wins']),
                        'total_match' => (empty($userParamList['total_match'])?$user['total_match']:$userParamList['total_match']), 
                        'total_winrate' => $win_rate,
                        'master_stadium_id' => (empty($userParamList['master_stadium_id'])?$user['master_stadium_id']:$userParamList['master_stadium_id']),
                        'cube_arr' => $cId, 
                        'cube_bonus' =>  (empty($cubeBonus['cube_id'])?"":$cubeBonus),
                        'total_xp' => $user['xp'],
                        'total_gold' => (empty($userParamList['gold'])?$user['gold']:$userParamList['gold']),
                        'total_relic' => (($this->winStatus == BATTLE_DRAW_STATUS || empty($roomUserActive))?$user['relics']:$userParamList['relics']),
                        'relic_bonus' => (empty($roomUserActive)?0:$matchStatusReward['relics']),
                        'xp_bonus' => (empty($roomUserActive)?0:$matchStatusReward['xp']),
                        'gold_bonus' => (empty($roomUserActive)?0:$matchStatusReward['gold']),
                        'cube_id_message' => "1-Titanium; 2- Diamond; 3- Platinum",
                        'win_status_message' => "1-Win; 2-Lost; 3-Draw",
                        'achievement' => $achieved,
                        'is_badge_given' => $isBadgeGiven,
                        'current_badge' => empty($latestBadge['master_badge_id'])?0:$latestBadge['master_badge_id']
                      );

    $this->setResponse('SUCCESS');
    return $responseFormat;
  }
}
