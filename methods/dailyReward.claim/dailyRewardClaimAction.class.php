<?php
/**
 * Author : Abhijth Shetty
 * Date   : 28-05-2019
 * Desc   : This is a controller file for dailyRewardClaim Action 
 */
class dailyRewardClaimAction extends baseAction{
   /**
   * @OA\Get(path="?methodName=dailyReward.claim", tags={"Rewards"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="daily_reward_id", name="daily_reward_id", description="The daily_reward_id specific to this event",
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
    $dailyReward = autoload::loadLibrary('queryLib', 'dailyReward');
    $inventoryLib = autoload::loadLibrary('queryLib', 'inAppPurchase');
    $userLib = autoload::loadLibrary('queryLib', 'user');
	$cardLib = autoload::loadLibrary('queryLib', 'card');
    $result = new arrayObject();
    $today = date('Y-m-d');


    //check id daily special offer claimed
    $userDailyReward = $dailyReward->getUserDailySpecialOfferForGivenDay($this->userId);
    
    if(!empty($userDailyReward) && $userDailyReward['status'] == CONTENT_ACTIVE)
    {
      $this->setResponse('DAILY_REWARD_CLAIMED');
      return $result;
    }

    if(empty($userDailyReward) || $userDailyReward['daily_reward_id'] != $this->dailyRewardId)
    {
      $this->setResponse('CUSTOM_ERROR', array('error' => 'Invalid daily reward id'));
      return $result;
    }
    $result['daily_reward'] = new arrayObject();

    $dailySpecialOffer = $dailyReward->getMasterDailyRewardDetail($this->dailyRewardId);
    
    $dailyRewardItem = $dailyReward->getMasterDailyRewardItem($dailySpecialOffer['master_daily_reward_id']);

    foreach($dailyRewardItem as $item) {

      $userDetail = $userLib->getUserDetail($this->userId);

      if($item['reward_type'] == DAILY_REWARD_TYPE_INVENTORY) {
        $rewardItem = $inventoryLib->getMasterInventoryDetail($item['reward_item_id']);
        $temp['type'] = ($rewardItem['type'] == CRYSTAL_INVENTORY) ? 'crystal' : 'gold';
        $userLib->updateUser($this->userId, array($temp['type'] => $userDetail[$temp['type']] + $rewardItem['quantity']));
           
      } else if($item['reward_type'] == DAILY_REWARD_TYPE_CARD) {

        $rewardItem = $cardLib->getMasterCardDetail($item['reward_item_id']);
        $userCardDetail = $cardLib->getUserCardDetailForMastercardId($this->userId, $item['reward_item_id']);
        if(empty($userCardDetail)) {
          $cardLib->insertUserCard(array(
            'user_id' => $this->userId,
            'master_card_id' => $item['reward_item_id'],
            'level_id' => DEFAULT_CARD_LEVEL_ID,
            'user_card_count' => $item['count'],
            'is_deck' => CONTENT_INACTIVE,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => CONTENT_ACTIVE
          ));
        } else {
          $cardLib->updateUserCard($userCardDetail['user_card_id'], array("user_card_count" => $userCardDetail['user_card_count'] + $item['count']));
        }
      } else {
        $rewardItem = $inventoryLib->getMasterCubeInventoryDetail($item['reward_item_id']);
        $userLib->updateUser($this->userId, array('crystal' => $userDetail['crystal'] + $rewardItem['amount']));
      }
    }
    if(empty($userDailyReward)) {
      $dailyReward->insertUserDailyReward(array(
        'user_id' => $this->userId,
        'daily_reward_id' => $this->dailyRewardId,
        'created_at' => date('Y-m-d H:i:s'),
        'status' => CONTENT_ACTIVE
      ));
    } else {
      $dailyReward->updateUserDailyReward($userDailyReward['user_daily_reward_id'], array('status' => CONTENT_ACTIVE));
    }
    

    $reward = $dailyReward->getDailySpecialOfferDetails($dailySpecialOffer);
    
    //time left for the next reward update
    $remainingTime = (strtotime($userDailyReward['created_at']) + 86400) - time();
    $reward['time_left'] = $remainingTime > 0 ? $remainingTime : 0;
    $result['daily_reward'] = $reward;

    $this->setResponse('SUCCESS');
    return $result;
  }  
}