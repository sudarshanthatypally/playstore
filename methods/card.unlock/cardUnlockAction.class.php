<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for cardUnlock Action
 */
class cardUnlockAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=card.unlock", tags={"Cards"}, 
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
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');

    $result = array();

    $cardDetail = $cardLib->getMasterCardDetail($this->masterCardId);
    $userCard = $cardLib->getUserCardDetailForMasterCardId($this->userId, $this->masterCardId);

    //User already has given card then increase the card count
    if(!empty($userCard))
    {
      $cardLib->updateUserCard($userCard['user_card_id'], array("user_card_count" => $userCard['user_card_count']+1));
      $userCard = $cardLib->getUserCardDetailForMasterCardId($this->userId, $this->masterCardId);
      $result['user_card_id'] = $userCard['user_card_id'];
      $result['total_card'] = $userCard['user_card_count'];
    }

    //If user gettting the card for the first time then add to user card for that user
    if(!empty($cardDetail) && empty($userCard))
    {
      $userCardId =$rewardLib->addRewardedCard($this->userId, $this->masterCardId);
      $result['user_card_id'] = $userCardId;
      $result['total_card'] = DEFAULT_CARD_COUNT;
    }

    if(empty($cardDetail))
    {
      $this->setResponse('FAILED');
      return new ArrayObject();
    }

    $this->setResponse('SUCCESS');
    return $result;
  }
}
