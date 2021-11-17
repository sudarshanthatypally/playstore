<?php
/**
 * Author : Abhijth Shetty
 * Date   : 03-01-2018
 * Desc   : This is a controller file for cardLevelUp Action
 */
class cardLevelUpAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=card.levelUp", tags={"Cards"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="master_card_id", name="master_card_id", description="The master_card_id specific to this event",
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
    $result = $propertyList = array();

    //Card Level up happens based on card count and some xp given to the user
    $user = $userLib->getUserDetail($this->userId);
    $userCard = $cardLib->getUserCardDetailForMastercardId($this->userId, $this->masterCardId);

    if(empty($userCard))
    {
      $this->setResponse('CARD_NOT_FOUND_IN_COLLECTION'); //change the response
      return new ArrayObject();
    }

    if($userCard['card_max_level'] < ($userCard['level_id']+1))
    {
      $this->setResponse('NO_MORE_CARD_LEVEL_UPGRADE');
      return new ArrayObject();
    }

    $cardPropertyList = $cardLib->getUserCardWithProperty($this->userId, $this->masterCardId);

    //get next level card property value
    $cardPropertyValue = $cardLib->getCardPropertyLevelUpgradeDetail($this->masterCardId, $userCard['level_id']+1);
    $levelUpgradeCardDetail = $cardLib->getMasterCardLevelUpgradeForCardCount($userCard['level_id']+1, $userCard['card_rarity_type']);

    //Insufficient card Count to level up the card.
    if($userCard['user_card_count'] < $levelUpgradeCardDetail['card_count'])
    {
      $this->setResponse('CARD_IS_NOT_ENOUGH');
      return new ArrayObject();
    }

    //Incufficient Gold
    if($user['gold'] < $levelUpgradeCardDetail['gold'])
    {
      $this->setResponse('GOLD_IS_NOT_ENOUGH');
      return new ArrayObject();
    }

    //Level up the card
    $cardLib->updateUserCard($userCard['user_card_id'], array("level_id" => $userCard['level_id']+1, "user_card_count" => $userCard['user_card_count'] - $levelUpgradeCardDetail['card_count']));
    $userLib->updateUser($this->userId, array("gold" => $user['gold'] - $levelUpgradeCardDetail['gold'], "xp" => $user['xp'] + $levelUpgradeCardDetail['xp']));

    foreach($cardPropertyList as $cardProperty)
    {
      if($cardProperty['is_default'] != CONTENT_ACTIVE){
        $temp = array();
        $temp['property_id'] = $cardProperty['property_id'];
        $temp['property_name'] = $cardProperty['property_name'];
        $propertyValue = $cardLib->getCardPropertyValue($this->masterCardId, $userCard['level_id']+1, $cardProperty['card_property_id']);
        $propertyForwardValue = $cardLib->getCardPropertyValue($this->masterCardId, $userCard['level_id']+2, $cardProperty['card_property_id']);
        $temp['property_value'] = empty($propertyValue['card_property_value'])?$cardProperty['user_card_property_value']:$propertyValue['card_property_value'];
        $temp['property_update_bonus'] = empty($propertyForwardValue['card_property_value'])?0:$propertyForwardValue['card_property_value']-$propertyValue['card_property_value'];
        $propertyList[] = $temp;
        $cardLib->updateUserCardProperty($cardProperty['user_card_property_id'], array(
                        'user_card_property_value' => empty($propertyValue['card_property_value'])?$cardProperty['user_card_property_value']:$propertyValue['card_property_value'],
                        'created_at' => date('Y-m-d H:i:s')));
      }
    }

    $isLevelIncreased =  $userLib->checkForUserLevelUp($this->userId);
    $user = $userLib->getUserDetail($this->userId);
    $userCard = $cardLib->getUserCardDetailForMastercardId($this->userId, $this->masterCardId);

    $result["level_id"] = $user['level_id'];
    $result["is_level_increased"] = $isLevelIncreased;
    $result["card_level"] = $userCard['level_id'];
    $result["total_card"] = $userCard['user_card_count'];
    $result["total_gold"] = $user['gold'];
    $result["total_crystal"] = $user['crystal'];
    $result["total_xp"] = $user['xp'];
    $result["xp_bonus"] = $levelUpgradeCardDetail['xp'];
    $levelUpgradeCardDetail = $cardLib->getMasterCardLevelUpgradeForCardCount($userCard['level_id']+1, $userCard['card_rarity_type']);
    $result["xp_bonus"] = $levelUpgradeCardDetail['xp'];
    $result["next_level_xp_bonus"] = $levelUpgradeCardDetail['xp'];
    $result["next_level_gold_cost"] = $levelUpgradeCardDetail['gold'];
    $result["next_level_card_count"] = $levelUpgradeCardDetail['card_count'];
    $result["property_List"] = $propertyList;

    $this->setResponse('SUCCESS');
    return $result;
  }
}
