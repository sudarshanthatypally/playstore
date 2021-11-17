<?php
/**
 * Author : Abhijth Shetty
 * Date   : 27-05-2019
 * Desc   : This is a controller file for cardGetDailyCards Action 
 */
class cardGetDailyCardsAction extends baseAction{
   /**
   * @OA\Get(path="?methodName=card.getDailyCards", tags={"Cards"}, 
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
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $dailyCard = autoload::loadLibrary('queryLib', 'dailyCard');
    $result = $temp = $masterCardList = array();

    $userDetail = $userLib->getUserDetail($this->userId);
    $dailyCardDetail = $dailyCard->getUserDailyCardDetail($this->userId);
    //$masterCardList = formatArr($cardLib->getMasterCardListForStadium($userDetail['master_stadium_id']), 'master_card_id');
    //$getCardVal= $cardLib->getMasterCardListForStadiumWithUserId($this->userId); //old one
    $getCardVal= $cardLib->getMasterCardListForStadiumWithUserIdWithVersion($this->userId, $this->androidVerId, $this->iosVerId); 
    //print_log($getCardVal);
    /*if(!empty($getCardVal['android_version_id']) && !empty($this->androidVerId)){
      if(version_compare($getCardVal['android_version_id'],$this->androidVerId, '<=')){
        $masterCardList = formatArr($getCardVal,'master_card_id');
      }
    }
    if(!empty($getCardVal['ios_version_id']) && !empty($this->iosVerId)){
      if(version_compare($getCardVal['ios_version_id'],$this->iosVerId, '<=')){
        $masterCardList = formatArr($getCardVal,'master_card_id');
      }
    }*/
    $masterCardList = formatArr($getCardVal,'master_card_id'); 
    //print_log($this->userId);
    //print_log($userDetail['master_stadium_id']);
    //print_log($masterCardList);
    if(!empty($dailyCardDetail) && (strtotime($dailyCardDetail['created_at']) + 28800) >= time()) {
      //show same cards for 8 hours
      $randomcardList = explode(",",$dailyCardDetail['card_id']);
      $remainingTime = (strtotime($dailyCardDetail['created_at']) + 28800) - time();
    } else {
      //refresh card list after each 8 hours
      $randomcardList = array_rand($masterCardList, SHOP_CARD_COUNT);
      //print_log($randomcardList);
      $dailyCard->insertUserDailyCard(array(
        'user_id' => $this->userId,
        'card_id' => implode(",",$randomcardList),
        'created_at' => date('Y-m-d H:i:s'),
        'status' => CONTENT_ACTIVE
      ));
      $remainingTime = (strtotime(date('Y-m-d H:i:s')) + 28800) - time();
    } 
      
    foreach($randomcardList as $key=>$value) {
      $list = $masterCardList[$value];
      $userCardDetail = $cardLib->getUserCardDetailForMastercardId($this->userId, $list['master_card_id']);
      if(empty($userCardDetail)){
        $userCardDetail = $cardLib->getUserCardDetailForMastercardIdIfNull($list['master_card_id']);
      }
      $item['master_card_id'] = $list['master_card_id'];
      $cardId[] = $list['master_card_id'];
      $item['title'] = $list['title'];
      $item['card_type'] = $list['card_type'];
      $item['card_type_message'] = ($list['card_type'] == CARD_TYPE_CHARACTER) ? 'Character' : 'Power';
      $item['card_rarity_type'] = $list['card_rarity_type'];
      if(!empty($list['android_version_id']) && !empty($this->androidVerId)){
        if(version_compare($list['android_version_id'],$this->androidVerId, '<=')){
          $item['is_available'] = 1; 
        }else{
          $item['is_available'] = 0;  
        }
      }elseif(!empty($list['ios_version_id']) && !empty($this->iosVerId)){
        if(version_compare($list['ios_version_id'],$this->iosVerId, '<=')){
          $item['is_available'] = 1; 
        }else{
          $item['is_available'] = 0; 
        }
      }else{
        $item['is_available'] = $list['is_available'];
      }
      $item['rarity_type_message'] = ($list['card_rarity_type'] == CARD_RARITY_COMMON)?"Common":(($list['card_rarity_type'] == CARD_RARITY_RARE)?"Rare":(($list['card_rarity_type'] == CARD_RARITY_EPIC)?"Epic":"Ultra Epic"));
      $item['total_card'] = (!empty($userCardDetail)) && (!empty($userCardDetail['user_card_count']))  ? $userCardDetail['user_card_count'] : DEFAULT_CARD_COUNT;
      $item['card_level'] = (!empty($userCardDetail)) ? $userCardDetail['level_id'] : DEFAULT_CARD_LEVEL_ID;
      $levelUpgradeCardDetail = $cardLib->getMasterCardLevelUpgradeForCardCount($item['card_level']+1, $list['card_rarity_type']);
      $item['next_level_card_count'] = $levelUpgradeCardDetail['card_count'];
      $item['next_level_gold_cost'] = $levelUpgradeCardDetail['gold'];
      $item['card_cost'] = $userCardDetail['gold'];
      if(!empty($dailyCardDetail['sold_id'])){
        $cardsList=explode(",",$dailyCardDetail['sold_id']);
        if(in_array($list['master_card_id'], $cardsList)){
          $item['buy_status'] = 1;
        }else{
          $item['buy_status'] = 0;
        }
      }else{
        $item['buy_status'] = 0;
      }
      $temp[] = $item;
    }
    //print_log($temp);
    $result['next_update_time'] = $remainingTime;
    $result['daily_cards_list'] = $temp;
    $this->setResponse('SUCCESS');
    return $result;
  }  
}