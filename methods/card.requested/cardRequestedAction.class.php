<?php
/**
 * Author : Abhijth Shetty
 * Date   : 02-01-2018
 * Desc   : This is a controller file for cardList Action
 */
class cardRequestedAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=card.requested", tags={"Cards"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="android_version_id", name="android_version_id", description="The android_version_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="ios_version_id", name="ios_version_id", description="The ios_version_id specific to this event",
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

    if(date('D') == 'Sun') { 
      $is_sunday=1;
      $isRarityCount=3;
    } else {
      $is_sunday=0;
      $isRarityCount=2;
    }
    //get List of Cards which user has.
    $cardList = $cardLib->getUserCardListForRequestedUser($this->userId,$is_sunday);
    foreach ($cardList as $card) 
    {
      $cardPropertyInfo = $temp = array();
      //$temp['user_card_id'] = $card['user_card_id'];
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
      /*$temp['next_level_card_count'] = $cardLevelUpDetail['card_count'];
      $temp['next_level_gold_cost'] = $cardLevelUpDetail['gold'];
      $temp['next_level_xp_bonus'] = $cardLevelUpDetail['xp'];*/
      $temp['total_card'] = $card['user_card_count'];
      $temp['card_level'] = $card['level_id'];
      $temp['card_description'] = $card['card_description'];
      $result[] = $temp;
    }

    //$lockedCardList = $cardLib->getLockedMasterCardList($this->userId);
    $rarityArray = array(
      array("1", "40", "Common"),
      array("2", "40", "Rare"),
      array("3", "40", "Epic"),
      array("4", "40", "Ultra Epic")
  );
    //for($i=1;$i<=$isRarityCount;$i++){
      $rarityTypeList = array();
      //$lockedCardList = $cardLib->getMasterCardListForRequestWithVersion($this->androidVerId, $this->iosVerId, $i);
      foreach($rarityArray as $ra){
        if($temp['rarity_type']<$isRarityCount){
          $temp = array();
          $temp['rarity_type'] = $ra[0]; 
          $temp['max_count'] = $ra[1]; 
          $temp['title'] = $ra[2]; 
          $rarityTypeList[]= $temp; 
       }
      }
     /* foreach ($lockedCardList as $card)
      { 
        $temp = array();
        $temp['master_card_id'] = $card['master_card_id'];
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
        $rarityTypeList[]= $temp; 
      }*/
      $lockedCards=$rarityTypeList;
    //}

    
    $this->setResponse('SUCCESS');
    return array('card_list' => $result, 'rarity_wise_card_list' => $lockedCards);
  }
}
