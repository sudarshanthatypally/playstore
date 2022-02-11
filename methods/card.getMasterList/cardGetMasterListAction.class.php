<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for cardGetMasterList Action
 */
class cardGetMasterListAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=card.getMasterList", tags={"Cards"}, 
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
    $result = $cardId = $temp = array();

    // Get the List of all the Master Card
    $cardList = $cardLib->getMasterCardListWithStadium();
    foreach ($cardList as $card)
    {
      $cardPropertyInfo = $temp = array();
      $temp['master_card_id'] = $card['master_card_id'];
      $temp['title'] = $card['title'];
      $temp['master_stadium_id'] = empty($card['master_stadium_id']) ? MONKEY_STADIUM : $card['master_stadium_id'];
      $temp['card_description'] = $card['card_description'];
      $temp['card_type_message'] = ($card['card_type'] == CARD_TYPE_TROOP)?"Troop":(($card['card_type'] == CARD_TYPE_SPELL)?"Spell":"Building");
      $temp['card_type'] = $card['card_type'];
     /* print_log("==================== version =========================");
      print_log($this->iosVerId);
      print_log($this->androidVerId);
      print_log($card['android_version_id']);
      print_log("==================");
      print_log(abs($this->iosVerId));
      print_log(abs($this->androidVerId));
      print_log(abs($card['android_version_id']));
      print_log("====================================================================");*/
      /*if(empty($card['android_version_id']) || is_null($card['android_version_id']) || $card['android_version_id']==""){
        $temp['is_available'] = $card['is_available'];
      }elseif(empty($card['ios_version_id']) || is_null($card['ios_version_id']) || $card['ios_version_id']=="" ){
        $temp['is_available'] = $card['is_available'];
      }else*/
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
      $temp['rarity_type_message'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($card['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($card['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
      $temp['card_rarity_type'] = $card['card_rarity_type'];
      $temp['bundlename']=$card['bundlename'];
      $temp['android_bundlehash']=$card['android_bundlehash'];
      $temp['android_bundlecrc']=$card['android_bundlecrc'];
      $temp['ios_bundlehash']=$card['ios_bundlehash'];
      $temp['ios_bundlecrc']=$card['ios_bundlecrc'];
      $cardPropertyList = $cardLib->getMasterCardPropertyList($card['master_card_id']);
      $cardlvl = $cardLib->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($card['master_card_id']);
      $temp['card_level'] = $cardlvl['level_id'];
      foreach($cardPropertyList as $cardProperty)
      {
        $tempProperty = array();
        if($cardProperty['is_default'] == true){
          //$cardPropertyValue = $cardLib->getCardPropertyValue($card['master_card_id'],DEFAULT_CARD_LEVEL_ID,$cardProperty['card_property_id']);
          $cardPropertyValue = $cardLib->getCardPropertyValue($card['master_card_id'],$cardlvl['level_id'],$cardProperty['card_property_id']);
          $temp[$cardProperty['property_id']] = $cardPropertyValue['card_property_value'];
          //$temp[$cardProperty['property_id']] = $cardProperty['card_property_value'];
        } else
        {
          $tempProperty['property_id'] = $cardProperty['property_id'];
          $tempProperty['property_name'] = $cardProperty['property_name'];
          //$cardPropertyValue = $cardLib->getCardPropertyValue($card['master_card_id'],DEFAULT_CARD_LEVEL_ID,$cardProperty['card_property_id']);
          $cardPropertyValue = $cardLib->getCardPropertyValue($card['master_card_id'],$cardlvl['level_id'],$cardProperty['card_property_id']);
          $tempProperty['property_value'] = $cardPropertyValue['card_property_value'];
          $tempProperty['is_child'] = $cardProperty['is_child'];
          $tempProperty['show_info'] = $cardProperty['show_info'];
          
          //$tempProperty['property_value'] = $cardProperty['card_property_value'];
          $cardPropertyInfo[] = $tempProperty;
        }
      }

      $temp['property_list'] = $cardPropertyInfo;
      $result[] = $temp;  
      
    }

    $this->setResponse('SUCCESS');
    return array('card_list' => $result);
  }
}
