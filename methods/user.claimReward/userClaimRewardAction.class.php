<?php
/**
 * Author : Abhijth Shetty
 * Date   : 09-01-2018
 * Desc   : This is a controller file for userClaimReward Action
 */
class userClaimRewardAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=user.claimReward", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_reward_id", name="user_reward_id", description="The user_reward_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="claim_reward", name="claim_reward", description="The claim_reward specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="cube_upgrade_id", name="cube_upgrade_id", description="The cube_upgrade_id specific to this event",
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
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $cubeLib = autoload::loadLibrary('queryLib', 'cube');
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');

    $result = $paramList = array();

    $userReward = $userLib->getUserRewardDetail($this->userRewardId);
    $userDetail = $userLib->getUserDetail($this->userId);

    if(empty($userReward))
    {
      $this->setResponse('CUSTOM_ERROR', array('error'=>'Not enough circlet to claim the reward'));
      return new ArrayObject();
    }

    if(!empty($userReward))
    {
      $result['reward_status'] = $userReward['status'];

      //Reward already Claimed.
      if($userReward['status'] == CONTENT_CLOSED)
      {
        $this->setResponse('CUSTOM_ERROR', array('error'=>'Reward already claimed'));
        return new ArrayObject();
      }
      if($userReward['status'] == CUBE_ON_PROCESS)
      {
        $crystalDetail = $cubeLib->getCubeRewardDetailForStadium($userReward['cube_id'], $userReward['master_stadium_id']);  
        if($userDetail['crystal'] < $crystalDetail['crystal_cost'])
        {
          $this->setResponse('CRYSTAL_IS_NOT_ENOUGH');
          return new ArrayObject();
        }
      }
      //Claim copper cube.
      /*if($userReward['cube_id'] == CUBE_DYNAMITE && $userReward['status'] != CONTENT_CLOSED){
        $result = $rewardLib->claimCopperCubeReward($this->userId, $this->userRewardId, $this->claimReward);
        $cubeOpenedAchievement = $achievementLib->checkCubeOpenedAchivement($this->userId);
        $result['reward_unlock_time'] =0;
        if($this->claimReward == CONTENT_ACTIVE && $result['reward_unlock_time'] == 0)
        {
          $cardUnlockAchievement = $achievementLib->checkCardUnlockAchivement($this->userId);
        }
        $achieved = array_merge($cubeOpenedAchievement,$cardUnlockAchievement);
      }*/

      // //Claim bronze cube.
      // if($userReward['cube_id'] == CUBE_BRONZE && $userReward['status'] != CONTENT_CLOSED){
      //   $result = $rewardLib->claimBronzeCubeReward($this->userId, $this->userRewardId, $this->claimReward);
      //   $cubeOpenedAchievement = $achievementLib->checkCubeOpenedAchivement($this->userId);
      //   if($this->claimReward == CONTENT_ACTIVE && $result['reward_unlock_time'] == 0)
      //   {
      //     $cardUnlockAchievement = $achievementLib->checkCardUnlockAchivement($this->userId);
      //   }
      //   $achieved = array_merge($cubeOpenedAchievement,$cardUnlockAchievement);
      // }

      //Claim titanium, dimond, Platinum cube.
      if(($userReward['cube_id'] == CUBE_FIRECRACKER || $userReward['cube_id'] == CUBE_BOMB || $userReward['cube_id'] == CUBE_ROCKET || $userReward['cube_id'] == CUBE_METALBOMB || $userReward['cube_id'] == CUBE_DYNAMITE) && $userReward['status'] != CONTENT_CLOSED){
        if(!empty($this->cubeUpgradeId) && $this->cubeUpgradeId==3){
          $result = $rewardLib->claimCubeRewardedDuringMatch($this->userId, $this->userRewardId, $this->claimReward, $this->androidVerId, $this->iosVerId, array('cube_upgrade_id' => $this->cubeUpgradeId)); 
        }else {
          $result = $rewardLib->claimCubeRewardedDuringMatch($this->userId, $this->userRewardId, $this->claimReward, $this->androidVerId, $this->iosVerId);
        } 
        
        $cubeOpenedAchievement = $achievementLib->checkCubeOpenedAchivement($this->userId);
        if($this->claimReward == CONTENT_ACTIVE && $result['reward_unlock_time'] == 0)
        {
          $cardUnlockAchievement = $achievementLib->checkCardUnlockAchivement($this->userId);
        }
        $achieved = array_merge($cubeOpenedAchievement,$cardUnlockAchievement);
      }

      $userDetail = $userLib->getUserDetail($this->userId);
      $isLevelIncreased =  $userLib->checkForUserLevelUp($this->userId);
     
      print_log("=========================== userClaimRewardAction =================================");
      print_log("crystal_bonus::".$result['crystal_bonus']);
      print_log("crystal::".$result['crystal']);
      print_log("=========================== userClaimRewardAction END =================================");
       
      //$cubeRewardDetail = $cubeLib->getCubeRewardDetailForStadium($result['cube_id'], $result['master_stadium_id']);
      $formatedResult = array('reward_unlock_time' => (!empty($result['reward_unlock_time']))?$result['reward_unlock_time']:0,
                              'gold_bonus' => (!empty($result['gold_bonus']))?$result['gold_bonus']:0,
                              'crystal_bonus' => (!empty($result['crystal_bonus']))?$result['crystal_bonus']:0,
                              'total_crystal' => $userDetail['crystal'],
                              'total_gold' =>  $userDetail['gold'],
                              'user_crystal_bonus'=> $result['crystal'],
                              'card_count' => (!empty($result['total_card_in_cube']))?$result['total_card_in_cube']:0,
                              'epic_count' => (!empty($result['total_epic_card_in_cube']))?$result['total_epic_card_in_cube']:0,
                              'ultra_epic_count' => (!empty($result['total_ultra_epic_card_in_cube']))?$result['total_ultra_epic_card_in_cube']:0,
                              'cube_upgrade_id' => $this->cubeUpgradeId,
                              'rare_count' => (!empty($result['total_rare_card_in_cube']))?$result['total_rare_card_in_cube']:0, 
                              'card_details' => (!empty($result['card_details']))?$result['card_details']:[],
                              'cube_id' => (!empty($result['cube_id']))?$result['cube_id']:0,
                              'master_stadium_id' => (!empty($result['master_stadium_id']))?$result['master_stadium_id']:0,
                              'reward_status' => (!empty($result['reward_status']))?$result['reward_status']:0,
                              'cube_id_message' => "1-Fire Ceacker; 2- Bomb; 3- Rocket; 4.Dynamite; 5.Metal Bomb",
                              'reward_status_message' => "1- Not claimed; 2- On process; 3- Can claim; 10- Reward completed.",
                              'achievement' => empty($achieved)?array():$achieved,
                              'level_id' => $userDetail['level_id'],
                              'total_xp' => $userDetail['xp'],
                              'is_level_increased' => $isLevelIncreased
                            );
    }

    $this->setResponse('SUCCESS');
    return $formatedResult;
  }
}
