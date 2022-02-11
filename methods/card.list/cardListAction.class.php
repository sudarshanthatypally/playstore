<?php
/**
 * Author : Abhijth Shetty
 * Date   : 02-01-2018
 * Desc   : This is a controller file for cardList Action
 */
class cardListAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=card.list", tags={"Cards"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
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
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $deckLib = autoload::loadLibrary('queryLib', 'deck');
    $result = $cardId = $lockedCards = array();

    //get List of Cards which user has.
    $cardList = $cardLib->getUserCardListForUserId($this->userId);
    //$getCardCTIds = $cardLib->getCardDetails(1, 1, 1);
    /*$cardPrevList = $cardLib->getCardPrevious($this->userId);
    $resCard = array();
    foreach ($cardPrevList as $cplId) {
      $j=0;
      $tempCard = array();
      $tempCard[$j++] = $cplId['master_card_id'];
      $resCard[] = implode(',', $tempCard);
    }
    $checkPreviousCardList=$resCard;
    */
    /*
    $userDeck = $deckLib->getUserDeckDetail($this->userId);
    $deckCards = $userDeck['deck_details'];
    if(!empty($userDeck)){
      foreach ($cardList as $card) 
      {
        if(in_array($card['master_card_id'], array_values(array_column($deckCards[$userDeck['current_deck_number']]['cards'], 'master_id')))) {
          ($card['is_deck'] != CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE)) : false;
        } else {
          ($card['is_deck'] == CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE)) : false; 
        } 
      }
    } 
    $cardList = $cardLib->getUserCardListForUserId($this->userId);*/
    foreach ($cardList as $card) 
    {
      $cardPropertyInfo = $temp = array();
      $temp['user_card_id'] = $card['user_card_id'];
      $temp['master_card_id'] = $card['master_card_id'];
      $temp['master_stadium_id'] = $card['master_stadium_id'];
      $temp['title'] = $card['title'];
      $temp['card_type'] = $card['card_type'];

      if(!empty($card['android_version_id']) && !empty($this->androidVerId)){
        if(version_compare($card['android_version_id'],$this->androidVerId, '<=')){
          $temp['is_available'] = 1; 
        }else{
          $temp['is_available'] = 0; 
        }
      }elseif(!empty($card['ios_version_id']) && !empty($this->iosVerId)){
        if(version_compare($card['ios_version_id'],$this->iosVerId, '<=')){
          $temp['is_available'] = 1; 
        }else{
          $temp['is_available'] = 0; 
        }
      }else{
        $temp['is_available'] = $card['is_available'];
      }
      //$temp['is_available'] = $card['is_available'];
      $temp['card_type_message'] = ($card['card_type'] == CARD_TYPE_TROOP)?"Troop":(($card['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
      //($card['card_type'] == CARD_TYPE_CHARACTER)?"Character":"Power"; 
      $temp['card_rarity_type'] = $card['card_rarity_type'];
      $temp['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($card['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
      $temp['is_deck_message'] = ($card['is_deck'] == CONTENT_ACTIVE)?"in deck":"not in deck";
      $temp['is_deck'] = $card['is_deck'];
      $cardLevelUpDetail = $cardLib->getMasterCardLevelUpgradeForCardCount($card['level_id']+1, $card['card_rarity_type']);
      $temp['next_level_card_count'] = $cardLevelUpDetail['card_count'];
      $temp['next_level_gold_cost'] = $cardLevelUpDetail['gold'];
      $temp['next_level_xp_bonus'] = $cardLevelUpDetail['xp'];
      $temp['total_card'] = $card['user_card_count'];
      $temp['card_level'] = $card['level_id'];
      $temp['card_description'] = $card['card_description'];

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
      $cardPropertyList = $cardLib->getCardPropertyForMasterCardAndLevelIdAndCommonLevel($card['master_card_id'], $card['level_id'], $basic_lvl);
      //$cardPropertyList = $cardLib->getCardPropertyForUserIdAndLevelId($card['user_card_id'], $card['level_id']);
      //print_log($cardPropertyList);
      foreach($cardPropertyList as $cardProperty)
      {
        $tempProperty = array();
        if($cardProperty['is_default'] == true){
          $temp[$cardProperty['property_id']] = $cardProperty['card_property_value'];

          //$cardPropertyValue = $cardLib->getCardPropertyValue($card['master_card_id'],$cardlvl['level_id'],$cardProperty['card_property_id']);
          //$temp[$cardProperty['property_id']] = $cardProperty['card_property_value'];

        }else{       
          $tempProperty['property_id'] = $cardProperty['property_id'];
          $tempProperty['property_name'] = $cardProperty['property_name'];
          $tempProperty['property_value'] = $cardProperty['card_property_value'];
          
          $propertyValue = $cardLib->getCardPropertyValue($card['master_card_id'], $card['level_id']+1, $cardProperty['card_property_id']);
          //$tempProperty['property_update_bonus'] = 0;
          $tempProperty['property_update_bonus'] = !empty($propertyValue['card_property_value'])?($propertyValue['card_property_value']-$tempProperty['property_value']):0;
          $tempProperty['is_child'] = $cardProperty['is_child'];
          $tempProperty['show_info'] = $cardProperty['show_info'];
          $cardPropertyInfo[] = $tempProperty;
        }
      }
      $temp['property_list'] = $cardPropertyInfo;
      $result[] = $temp;
    }
    
    //$lockedCardList = $cardLib->getLockedMasterCardList($this->userId);
    $lockedCardList = $cardLib->getLockedMasterCardListWithVersion($this->userId, $this->androidVerId, $this->iosVerId);
    foreach ($lockedCardList as $card)
    {
      $temp = array();
      $temp['master_card_id'] = $card['master_card_id'];
      $temp['master_stadium_id'] = $card['master_stadium_id'];
      $temp['title'] = $card['title'];
      $temp['card_type'] = $card['card_type'];
      if(!empty($card['android_version_id']) && !empty($this->androidVerId)){
        if(version_compare($card['android_version_id'],$this->androidVerId, '<=')){
          $temp['is_available'] = 1; 
        }else{
          $temp['is_available'] = 0; 
        }
      }elseif(!empty($card['ios_version_id']) && !empty($this->iosVerId)){
        if(version_compare($card['ios_version_id'],$this->iosVerId, '<=')){
          $temp['is_available'] = 1; 
        }else{
          $temp['is_available'] = 0; 
        }
      }else{
        $temp['is_available'] = $card['is_available'];
      }
      //$temp['is_available'] = $card['is_available'];
      $temp['card_type_message'] = ($card['card_type'] == CARD_TYPE_TROOP)?"Troop":(($card['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
      $temp['card_rarity_type'] = $card['card_rarity_type'];
      $temp['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($card['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
      $temp['card_description'] = $card['card_description'];
      //$cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE));
      $lockedCards[] = $temp;
    }

    //$futureCardList = $cardLib->getFutureMasterCardList($this->userId);
    $futureCardList = $cardLib->getFutureMasterCardListWithVersion($this->userId, $this->androidVerId, $this->iosVerId);
    foreach ($futureCardList as $card)
    {
      $temp = array();
      $temp['master_card_id'] = $card['master_card_id'];
      $temp['master_stadium_id'] = $card['master_stadium_id'];
      $temp['title'] = $card['title'];
      $temp['card_type'] = $card['card_type'];
      if(!empty($card['android_version_id']) && !empty($this->androidVerId)){
        if(version_compare($card['android_version_id'],$this->androidVerId, '<=')){
          $temp['is_available'] = 1; 
        }else{
          $temp['is_available'] = 0; 
        }
      }elseif(!empty($card['ios_version_id']) && !empty($this->iosVerId)){
        if(version_compare($card['ios_version_id'],$this->iosVerId, '<=')){
          $temp['is_available'] = 1; 
        }else{
          $temp['is_available'] = 0; 
        }
      }else{
        $temp['is_available'] = $card['is_available'];
      }
      //$temp['is_available'] = $card['is_available'];
      $temp['card_type_message'] = ($card['card_type'] == CARD_TYPE_TROOP)?"Troop":(($card['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
      $temp['card_rarity_type'] = $card['card_rarity_type'];
      $temp['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($card['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
      $temp['card_description'] = $card['card_description'];
      //$cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE));
      $futureCards[] = $temp;
    }

    $this->setResponse('SUCCESS');
    return array('user_card_list' => $result, 'user_locked_card_list' => $lockedCards, 'user_future_card_list' => $futureCards);
  }
}
