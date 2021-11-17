<?php
/**
 * Author : Abhijth Shetty
 * Date   : 28-05-2019
 * Desc   : This is a controller file for dailyRewardGet Action 
 */
class dailyRewardGetAction extends baseAction{
/**
   * @OA\Get(path="?methodName=dailyReward.get", tags={"Rewards"}, 
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
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $dailyReward = autoload::loadLibrary('queryLib', 'dailyReward');
  
    $result = $reward = array();
    $reward = new arrayObject();
    $today = date('Y-m-d');
    $userDetail = $userLib->getUserDetail($this->userId);
    $status = CONTENT_INACTIVE;
    
    //check user recieved daily special reward for current day or not
    $userDailyReward = $dailyReward->getUserDailySpecialOfferForGivenDay($this->userId);

    if(!empty($userDailyReward) && (strtotime($userDailyReward['created_at']) + 86400) >= time())
    {
      if($userDailyReward['status'] != CONTENT_ACTIVE) {
        //show the same reward for a day
        $dailySpecialOffer = $dailyReward->getMasterDailyRewardDetail($userDailyReward['daily_reward_id']);
        $reward = $dailyReward->getDailySpecialOfferDetails($dailySpecialOffer);
        $reward['time_left'] = (strtotime($userDailyReward['created_at']) + 86400) - time();
        $status = CONTENT_ACTIVE;
      }
    } else
    {
      //randomly get daily special offer for a day
      $dailySpecialOffer = $dailyReward->getMasterDailyRewardRandomly($userDetail['master_stadium_id']);
      if($userDetail['master_stadium_id']==1){
        $dailySpecialOffer = $dailyReward->getMasterDailyRewardRandomly(2);
      }
      
      $reward = $dailyReward->getDailySpecialOfferDetails($dailySpecialOffer);

      $dailyReward->insertUserDailyReward(array(
        'user_id' => $this->userId,
        'daily_reward_id' => $dailySpecialOffer['master_daily_reward_id'],
        'created_at' => date('Y-m-d H:i:s'),
        'status' => CONTENT_INACTIVE
      ));
      $reward['time_left'] = (strtotime(date('Y-m-d H:i:s')) + 86400) - time();
      $status = CONTENT_ACTIVE;
    }
    $result['daily_reward'] = $reward;
    $result['reward_status'] = $status;
    $result['reward_status_message'] = "1. not claimed, 2. claimed";
    $this->setResponse('SUCCESS');
    return $result;
  }  
}
