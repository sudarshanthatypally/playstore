<?php
/**
 * Author : Abhijth Shetty
 * Date   : 11-04-2018
 * Desc   : This is a controller file for userRewardAdsBonus Action
 */
class userRewardAdsBonusAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=user.rewardAdsBonus", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="crystal", name="crystal", description="The crystal specific to this event",
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
    $result = new ArrayObject();
    date_default_timezone_set('Asia/Kolkata');
    $user = $userLib->getUserDetail($this->userId);

    if($this->crystal > 0)
    {
      $dailyCrystal = $userLib->getUserDailyAdReward($this->userId, date('Y-m-d'));
      $dailyCrystalOne = $userLib->getUserDailyAdRewardOne($this->userId, date('Y-m-d'));
      if(empty($dailyCrystal))
      {
        $userLib->updateUser($this->userId, array('crystal' => $user['crystal'] + $this->crystal));

        $user = $userLib->getUserDetail($this->userId);
        $result['total_crystal'] = $user['crystal'];
        $result['level_id'] = $user['level_id'];
        $result['total_gold'] = $user['gold'];

        $userLib->insertUserDailyAdReward(array('user_id' =>$this->userId,'created_at'=>date('Y-m-d'),'status' => CONTENT_ACTIVE));
      }else{
        $seconds = time() - strtotime($dailyCrystalOne['created_at']);

        $days = floor($seconds / 86400);
        $seconds %= 86400;

        $hours = floor($seconds / 3600);
        $seconds %= 3600;

        $minutes = floor($seconds / 60);
        $seconds %= 60;
        $result['message_toshow'] = "Next reward in :";
        if($days!=0){
          $result['message_toshow'] .= "$days days: "; 
        }
        if($hours!=0){
          if($hours<=1){
            $result['message_toshow'] .= "$hours hr: ";
          }else{
            $result['message_toshow'] .= "$hours hrs: ";
          }
          
        }
        if($minutes!=0){
          $result['message_toshow'] .= "$minutes min: ";
        }
        if($seconds!=0){
          $result['message_toshow'] .= "$seconds sec";
        }
        
        
        
      }
    }

    $this->setResponse('SUCCESS');
    return $result;
  }
}
