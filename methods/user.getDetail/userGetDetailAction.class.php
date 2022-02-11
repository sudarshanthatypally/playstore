<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for userGetDetail Action
 */
class userGetDetailAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=user.getDetail", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="level_up", name="level_up", description="The level_up specific to this event",
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
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    $badgeLib = autoload::loadLibrary('queryLib', 'badge');
    $deckLib = autoload::loadLibrary('queryLib', 'deck');
    $notificationLib = autoload::loadLibrary('queryLib', 'notification');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $questLib = autoload::loadLibrary('queryLib', 'quest');
    date_default_timezone_set('Asia/Kolkata');
    $result = $deckList = $temp = array();
    $notificationLib->deleteNotificationByDate();
    $kingdomLib->deleteMsgByDate();
    $kingdomLib->deleteKindomMsgByDate();
    $kingdomLib->deleteBattleRecordsByDate();
    //User detail with cards list which is in use deck.
    $userLib->checkForUserLevelUp($this->userId);
    $currDate= date('Y-m-d H:i:s');
    $qv = $questLib->getQuestCollectFreeReward($this->userId, $this->androidVerId, $this->iosVerId);
    if(empty($qv)){
      $questLib->insertMasterQuestInventory(array(
        'quest_id' => 1,
        'time' => date('Y-m-d H:i:s'),
        'user_id' => $this->userId,
        'status' => CONTENT_ACTIVE,
        'created_at' => date('Y-m-d H:i:s')));
    }
    


    if($this->levelUp==1){
      $userLib->updateUser($this->userId, array('level_up' => 0));
    }
    
    if($this->stadiumlevelUp==1){
      $userLib->updateUser($this->userId, array('stadium_level_up' => 0));
    }
    


    $userDetail = $userLib->getUserDetail($this->userId);
    
    /*$qUserStadiumv = $questLib->getQuestUserStadium5Reward($this->userId);
    if($qUserStadiumv['slide_count']>=$qUserStadiumv['slide_maxvalue']){
      $questData= $questLib->getBattleQuestData(7,$this->userId);
      if(empty($questData)){
        $questLib->insertMasterQuestInventory(array(
          'quest_id' => 7,
          'time' => date('Y-m-d H:i:s'),
          'user_id' => $this->userId,
          'status' => CONTENT_ACTIVE,
          'slide_count'=>1,
          'created_at' => date('Y-m-d H:i:s')));
      }else{
        $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('slide_count' => $userDetail['master_stadium_id']));
        if($userDetail['master_stadium_id']>=5){
          $questLib->updateQuestInventory($questData['quest_id'], $this->userId, array('slide_count' => 5));
        } 
      }
      
    }*/

    if($userDetail['max_stadium_id']=="" || $userDetail['max_stadium_id']==0){
      $userLib->updateUser($this->userId, array('max_stadium_id' => $userDetail['master_stadium_id']));
    }
    $userDetail = $userLib->getUserDetail($this->userId);
    $userDeck = $deckLib->getUserDeckDetail($this->userId);
    if(!empty($userDeck)) {
      $deckData = json_decode($userDeck['deck_data'],true);
      $deckCards = formatArr($deckData['deck_details'], 'deck_id');
      $data = (array_column($deckCards[$deckData['current_deck_number']]['cards'], 'master_id'));
      $userCardList = $cardLib->getUserCardForCurrentDeck($this->userId, DECK_ACTIVE, implode(',',$data)); 
      if(empty($userCardList) || count($userCardList)==0){
        $userCardList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE);
      }
    } else {
      $userCardList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE); 
    }
    

    $result['name'] = $userDetail['name'];
    $result['total_wins'] = $userDetail['total_wins'];
     $result['total_match'] = $userDetail['total_match'];
     $result['total_winrate'] = (!empty($userDetail['total_match'])||$userDetail['total_match']>0)?(($userDetail['total_wins']/$userDetail['total_match'])*100):0;
    $result['total_circlet'] = $userDetail['circlet'];    
    $result['level_id'] = $userDetail['level_id'];
    $result['total_relic'] = $userDetail['relics'];
    $result['total_crystal'] = $userDetail['crystal'];
    $result['total_gold'] = $userDetail['gold'];
    $result['xp'] = $userDetail['xp'];
    $result['facebook_id'] = $userDetail['facebook_id'];
    $result['google_id'] = $userDetail['google_id'];
    $result['is_alert'] = $userDetail['is_alert'];
    $result['is_login'] = $userDetail['is_login'];
    $result['kingdom_id'] = $userDetail['kingdom_id'];
    $result['game_center_id'] = $userDetail['game_center_id'];
    if(!empty($userDetail['user_uid'])){
      $result['user_uid'] = $userDetail['user_uid'];
    }else{
      $randVal = rand(7,9);
      $user_uid = $userLib->secure_random_string($randVal);
      $userLib->updateUser($this->userId, array(
        'user_uid' => strtoupper($user_uid),
      ));
      $result['user_uid'] = strtoupper($user_uid);
    }    

    $result['master_stadium_id'] = $userDetail['master_stadium_id'];
    $result['god_tower_health'] = $userDetail['god_tower_health'];
    $result['stadium_tower_health'] = $userDetail['stadium_tower_health'];
    $result['god_tower_damage'] = $userDetail['god_tower_damage'];
    $result['stadium_tower_damage'] = $userDetail['stadium_tower_damage'];
    $previousLevelUpXp = $cardLib->getMasterLevelUpXpForUserLevel($userDetail['level_id']-1);
    $presentLevelUpXp = $cardLib->getMasterLevelUpXpForUserLevel($userDetail['level_id']);
    $result['god_tower_health_bonus']=$presentLevelUpXp['god_tower_health']-$previousLevelUpXp['god_tower_health'];
    $result['god_tower_damage_bonus']=$presentLevelUpXp['god_tower_damage']-$previousLevelUpXp['god_tower_damage'];
    $result['stadium_tower_damage_bonus']=$presentLevelUpXp['stadium_tower_damage']-$previousLevelUpXp['stadium_tower_damage'];
    $result['stadium_tower_health_bonus']=$presentLevelUpXp['stadium_tower_health']-$previousLevelUpXp['stadium_tower_health'];
    foreach ($userCardList as $card)
    {
      $cardPropertyInfo = $temp = array();
      $temp['user_card_id'] = $card['user_card_id'];
      $temp['master_card_id'] = $card['master_card_id'];
      $temp['title'] = $card['title'];
      $temp['card_type'] = $card['card_type'];
      $temp['bundlename'] = $card['bundlename'];
      $temp['android_bundlehash']=$card['android_bundlehash'];
      $temp['android_bundlecrc']=$card['android_bundlecrc'];
      $temp['ios_bundlehash']=$card['ios_bundlehash'];
      $temp['ios_bundlecrc']=$card['ios_bundlecrc'];
      $temp['is_available'] = $card['is_available'];
      $temp['card_type_message'] = ($card['card_type'] == CARD_TYPE_TROOP)?"Troop":(($card['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
      $temp['card_rarity_type'] = $card['card_rarity_type'];
      $temp['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($card['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
      $temp['is_deck_message'] = ($card['is_deck'] == CONTENT_ACTIVE)?"in deck":"not in deck";
      $temp['is_deck'] = $card['is_deck'];
      $cardLevelUpDetail = $cardLib->getMasterCardLevelUpgradeForCardCount($card['level_id']+1, $card['card_rarity_type']);
      $temp['next_level_card_count'] = $cardLevelUpDetail['card_count'];
      $temp['next_level_gold_cost'] = $cardLevelUpDetail['gold'];
      $temp['total_card'] = $card['user_card_count'];
      $temp['card_level'] = $card['level_id'];
      $temp['card_description'] = $card['card_description'];

      //$cardPropertyList = $cardLib->getCardPropertyForUseCardId($card['user_card_id']);
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
    $cardPropertyList = $cardLib->getCardPropertyForMasterCardAndLevelIdAndCommonLevel($card['master_card_id'], $card['level_id'], $basic_lvl);
           
    //$cardPropertyList = $cardLib->getCardPropertyForUseCardIdAndLevelId($card['user_card_id'], $card['level_id']);
      
      foreach($cardPropertyList as $cardProperty)
      {
        $tempProperty = array();
        if($cardProperty['is_default'] == true){
          $temp[$cardProperty['property_id']] = $cardProperty['card_property_value'];
        } else
        {
          $tempProperty['property_id'] = $cardProperty['property_id'];
          $tempProperty['property_name'] = $cardProperty['property_name'];
          $tempProperty['property_value'] = $cardProperty['card_property_value'];
          $tempProperty['is_child'] = $cardProperty['is_child'];
          $tempProperty['show_info'] = $cardProperty['show_info'];
          $cardPropertyInfo[] = $tempProperty;
        }
      }
      $temp['property_list'] = $cardPropertyInfo;
      $deckList[] = $temp;
    }

    $rejectedList = $notificationLib->getRejectedDetailByUsers($this->userId);
    $rejectedListInfo=array();
    foreach($rejectedList as $rejlist)
    {
      
      $tempRejProperty = array();
      $rejData = json_decode($rejlist['data'], true);
      $kingdomDetails= $kingdomLib->getKingdomDetails($rejData['kingdom_id']);
      $tempRejProperty['notification_id'] = $rejlist['notification_id'];
      $tempRejProperty['kingdom_id'] = $rejData['kingdom_id'];
      $tempRejProperty['kingdom_rejectmsg'] = "Your request to join kingdom ".$kingdomDetails['kingdom_name']." has been declined.";
      $rejectedListInfo[] = $tempRejProperty;
    }
    //$kingdomRejectedList[] = $rejectedListInfo;
    $msg_count = $kingdomLib->getKingdomUnseenMsgCount($userDetail['kingdom_id'], $userDetail['notify_seen_count'],$this->userId);
    $result['notification_msg_count'] = $msg_count;
    $result['kingdom_rejected_list'] = $rejectedListInfo;
    //print_log("test completed");
    $result['deck_list'] = $deckList;
    $result['notification_status'] = $userDetail['notification_status'];
    $result['notification_status_message'] = "1-Active; 0-inActive";
    $result['is_tutorial_completed'] = $userDetail['is_tutorial_completed'];
    $result['is_kathika_tutorial_completed'] = $userDetail['is_kathika_tutorial_completed'];
    $result['is_storybook_tutorial_completed'] = $userDetail['is_storybook_tutorial_completed'];
    $result['tutorial_seq'] = $userDetail['tutorial_seq'];
    $result['editname_count'] = $userDetail['editname_count'];
    //$result['android_update'] = false;
    //$result['ios_update'] = false;


    $serverConfig=$userLib->getServerConfig();

    $result['android_appversion'] = $serverConfig['android_version'];
    $result['ios_appversion'] = $serverConfig['ios_version'];

    if(!empty($result['android_appversion']) && !empty($this->androidVerId)){
      $userLib->updateUser($this->userId, array('current_version' => "a-".$this->androidVerId));
      if(version_compare($result['android_appversion'],$this->androidVerId, '<=')){
        $result['android_update'] = false; 
      }else{
        $result['android_update'] = true;
      }
    }elseif(!empty($result['ios_appversion']) && !empty($this->iosVerId)){
      $userLib->updateUser($this->userId, array('current_version' => "i-".$this->iosVerId));
      if(version_compare($result['ios_appversion'],$this->iosVerId, '<=')){
        $result['ios_update'] = false;
      }else{
        $result['ios_update'] = true;
      }
    }else{
      $result['android_update'] = false;
      $result['ios_update'] = false;
    }
    //$result['updatedurl'] = "Share Links \n iOS=https://apps.apple.com/gb/app/epiko-regal/id1576311776 \n Android=Coming Soon";
    $result['editname_cost'] = 500;
    $result['android_updatedurl']="Share Links \n  Android=Coming Soon";
    $result['ios_updatedurl']= "Share Links \n iOS=https://apps.apple.com/gb/app/epiko-regal/id1576311776";
    $result['invite_baseurl']="https://epikoregal.com/";
    $result['invite_prefixurl']="friendlybattle.page.link";
    $result['maintainanceon'] =$serverConfig['is_server'];
    $result['level_up'] = $userDetail['level_up'];
    $result['stadium_level_up'] = $userDetail['stadium_level_up'];
    $result['max_stadium_id'] = $userDetail['max_stadium_id'];
    $result['kingQueen_status'] = $userDetail['kingQueen_status'];
    $result['season_id'] = $userDetail['season_id'];
    if($userDetail['season_id']==0){
      $result['is_season_available']=0;
    }
    //settype($result['android_appversion'], "float");
    //settype($result['IOS_appversion'], "float");
    //settype($result['updatedurl'], "url");
    

    $dailyCrystal = $userLib->getUserDailyAdReward($this->userId, date('Y-m-d'));

    $result['is_rewarded_ad_shown'] = !empty($dailyCrystal)?CONTENT_ACTIVE:CONTENT_INACTIVE;
    $winStreak = $roomLib->getUserWinStreak($this->userId);
    $result['win_streak'] = empty($winStreak['win_streak'])?0:$winStreak['win_streak'];

    $latestBadge = $badgeLib->getUserLatestBadge($this->userId);
    $result['current_badge'] = empty($latestBadge['master_badge_id'])?0:$latestBadge['master_badge_id'];

    //dev reference, we can remove going frwd
    $result['android_push_token'] = $userDetail['android_push_token'];
    $result['ios_push_token'] = $userDetail['ios_push_token'];

    $this->setResponse('SUCCESS');
    return $result;
  }
}
