<?php
/**
 * Author : Abhijth Shetty
 * Date   : 27-05-2019
 * Desc   : This is a controller file for cardPurchase Action 
 */
class cardPurchaseAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=card.purchase", tags={"Cards"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="master_card_id", name="master_card_id", description="The master_card_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="count", name="count", description="The count specific to this event",
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
    $dailyCard = autoload::loadLibrary('queryLib', 'dailyCard');
    $result = new arrayObject();
    
    $dailyCardDetail = $dailyCard->getUserDailyCardDetail($this->userId);
    //$cardsParchased=array();
    $cardsParchased= array($dailyCardDetail['sold_id']);
    $masterCardDetail = $cardLib->getMasterCardDetail($this->masterCardId);
    $userDetail = $userLib->getUserDetail($this->userId);
    $goldrequired = $this->count * $masterCardDetail['gold'];

    if($userDetail['gold'] < $goldrequired) {
      $this->setResponse('GOLD_IS_NOT_ENOUGH');
      return new ArrayObject();
    }
    
      if(empty($cardsParchased) || count($cardsParchased)==0){
        $cardsParchased = substr($cardsParchased, 1);
      }
   //// }else{ 
      if (!in_array($this->masterCardId, $cardsParchased)) {
        array_push($cardsParchased, $this->masterCardId); 
      }else{
        $cardsParchased= $this->masterCardId; 
      }
      //$cardsParchased = explode(",",$cardsParchased);
      //$cardsParchased=array_unique($cardsParchased);
   // } 
   
   $cardsParchased = array_filter($cardsParchased);
   $cardsParchased=implode(",",$cardsParchased);

    $userLib->updateUser($this->userId, array('gold' => $userDetail['gold'] - $goldrequired));
    $userCardDetail = $cardLib->getUserCardDetailForMastercardId($this->userId, $this->masterCardId);
    if(empty($userCardDetail)) {
      $cardLib->insertUserCard(array(
        'user_id' => $this->userId,
        'master_card_id' => $this->masterCardId,
        'level_id' => DEFAULT_CARD_LEVEL_ID,
        'user_card_count' => $this->count,
        'is_deck' => CONTENT_INACTIVE,
        'created_at' => date('Y-m-d H:i:s'),
        'status' => CONTENT_ACTIVE
      ));
      $dailyCard->updateUserDailyCard($this->userId,array("sold_id" => $cardsParchased));
    } else {
      
      $dailyCard->updateUserDailyCard($this->userId,array("sold_id" => $cardsParchased));
      $cardLib->updateUserCard($userCardDetail['user_card_id'], array("user_card_count" => $userCardDetail['user_card_count'] + $this->count));
    }

    $this->setResponse('SUCCESS');
    return $result;
  }  
}