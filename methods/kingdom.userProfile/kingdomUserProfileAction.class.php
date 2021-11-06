<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 23-11-2020
 * Desc   : This is a controller file for userGetDetail Action
 */
class kingdomUserProfileAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=kingdom.userProfile", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="search_user_id", name="search_user_id", description="The search_user_id specific to this event",
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
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $result = $deckList = $temp = array();

    $userDetail = $userLib->getUserDetail($this->searchUserId);
    $userDeck = $deckLib->getUserDeckDetail($this->searchUserId);
    if(!empty($userDeck)) {
      $deckData = json_decode($userDeck['deck_data'],true);
      $deckCards = formatArr($deckData['deck_details'], 'deck_id');
      $data = (array_column($deckCards[$deckData['current_deck_number']]['cards'], 'master_id'));
      $userCardList = $cardLib->getUserCardForCurrentDeck($this->searchUserId, DECK_ACTIVE, implode(',',$data)); 
    } else {
      $userCardList = $cardLib->getUserCardForActiveDeck($this->searchUserId, DECK_ACTIVE); 
    }
    
    $result['user_id'] = $this->searchUserId;
    $result['name'] = $userDetail['name'];
    $result['total_wins'] = $userDetail['total_wins'];
     $result['total_match'] = $userDetail['total_match'];
     $result['total_winrate'] = (!empty($userDetail['total_match'])||$userDetail['total_match']>0)?(($userDetail['total_wins']/$userDetail['total_match'])*100):0;
    $result['level_id'] = $userDetail['level_id'];
    $result['total_circlet'] = $userDetail['circlet'];
    $result['total_relic'] = $userDetail['relics'];
    $result['total_crystal'] = $userDetail['crystal'];
    $result['facebook_id'] = $userDetail['facebook_id'];
    $result['google_id'] = $userDetail['google_id'];
    $result['kingdom_id'] = $userDetail['kingdom_id'];
    $result['master_stadium_id'] = $userDetail['master_stadium_id'];
    foreach ($userCardList as $card)
    {
      $cardPropertyInfo = $temp = array();
      $temp['user_card_id'] = $card['user_card_id'];
      $temp['master_card_id'] = $card['master_card_id'];
      $temp['title'] = $card['title'];
      $temp['card_type'] = $card['card_type'];
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

      $cardPropertyList = $cardLib->getCardPropertyForUseCardId($card['user_card_id']);
      foreach($cardPropertyList as $cardProperty)
      {
        $tempProperty = array();
        if($cardProperty['is_default'] == true){
          $temp[$cardProperty['property_id']] = $cardProperty['user_card_property_value'];
        } else
        {
          $tempProperty['property_id'] = $cardProperty['property_id'];
          $tempProperty['property_name'] = $cardProperty['property_name'];
          $tempProperty['property_value'] = $cardProperty['user_card_property_value'];
          $cardPropertyInfo[] = $tempProperty;
        }
      }
      $temp['property_list'] = $cardPropertyInfo;
      $deckList[] = $temp;
    }
    //print_log("test completed");
    $result['deck_list'] = $deckList;
    $result['kingQueen_status'] = $userDetail['kingQueen_status'];
    $winStreak = $roomLib->getUserWinStreak($this->searchUserId);
    $result['win_streak'] = empty($winStreak['win_streak'])?0:$winStreak['win_streak'];
    $kuDetails= $kingdomLib->getKingdomUserDetailsWithUsersId($this->searchUserId);
    $requesterDetails= $kingdomLib->getKingdomUserDetailsWithUsersId($this->userId);
    if($requesterDetails['kingdom_id']==$kuDetails['kingdom_id']){
      $result['can_edit'] = ($requesterDetails['user_type']==2 || $requesterDetails['user_type']==3 && ($requesterDetails['user_type']<$kuDetails['user_type'] || $kuDetails['user_type']==1) )?true:false;
    }else{
      $result['can_edit'] =false;
    }
    $result['user_type']=$requesterDetails['user_type'];
    $result['search_user_type']=$kuDetails['user_type'];

    $this->setResponse('SUCCESS');
    return $result;
  }
}
