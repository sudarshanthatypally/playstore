<?php
/**
 * Author : Abhijth Shetty
 * Date   : 30-01-2018
 * Desc   : This is a controller file for cubeTimedBonus Action
 */
class cubeTimedBonusAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=cube.timedBonus", tags={"Cubes"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_reward_type", name="user_reward_type", description="The user_reward_type specific to this event",
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
    $cubeLib = autoload::loadLibrary('queryLib', 'cube');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $result =  array();

    $user = $userLib->getUserDetail($this->userId);

    if($this->userRewardtype == CUBE_COPPER){
      $userRewardList = $userLib->getUserRewardActiveListForCube($this->userId, $this->userRewardtype, CONTENT_CLOSED);
    }

    //Check User eligibity for the Bronze reward
    if($this->userRewardtype == CUBE_COPPER && empty($userRewardList))
    {
      $userReward = $rewardLib->checkEligibilityOfCopperReward($this->userId, $user['master_stadium_id']);
      $result['total_circlet'] = ($userReward['total_circlet'] == 0)?0:$userReward['total_circlet'];
      $result['reward_unlock_time'] = ($userReward['unlock_time']<=0)?0:$userReward['unlock_time'];
      $result['reward_status'] = ($userReward['unlock_time']<=0)?CUBE_ACTIVE:CUBE_ON_PROCESS;
    }

    //Check User eligibity for the Copper reward
    // if($this->userRewardtype == CUBE_COPPER && empty($userRewardList))
    // {
    //   $userRecentCopperRewardDetail = $rewardLib->getLastCubeRewardDetailForUser($this->userId, CUBE_COPPER);
    //   $unlockTime = (($userRecentCopperRewardDetail['claimed_at']) + UNLOCK_CUBE_COPPER_TIMEOUT);
    //   $unlockTime = ($unlockTime-time() <= 0)?0:$unlockTime-time();

    //   // $noReward =((strtotime($user['created_at']) + UNLOCK_CUBE_COPPER_TIMEOUT));
    //   // $noReward = ($noReward-time() <= 0)?0:$noReward-time();

    //   $userRecentCopperRewardDetail = $rewardLib->getLastCubeRewardDetailForUser($this->userId, CUBE_COPPER);
    //   $result['reward_unlock_time'] = (empty($userRecentCopperRewardDetail))?0:$unlockTime;
    //   $result['reward_status'] = ($result['reward_unlock_time'] <= 0)?CUBE_CAN_BE_CLAIMED:CUBE_ON_PROCESS;

    //   if($result['reward_unlock_time'] <= 0){
    //     $rewardLib->rewardCopperCube($user['user_id'], $user['master_stadium_id']);
    //   }
    // }
    $userRewardList = $userLib->getUserRewardActiveListForCube($this->userId, $this->userRewardtype, CONTENT_CLOSED);

    foreach($userRewardList as $reward)
    {
      $temp = array();
      $result['user_reward_id'] = $reward['user_reward_id'];
      $result['cube_id'] = $reward['cube_id'];
      $result['master_stadium_id'] = $reward['master_stadium_id'];
      $result['reward_status'] = $reward['status'];
    }

    $formatResult = array('user_reward_id' => (!empty($userRewardList))?$result['user_reward_id']:0,
                          'cube_id' => (!empty($userRewardList))?$result['cube_id']:0,
                          'master_stadium_id' => (!empty($userRewardList))?$result['master_stadium_id']:0,
                          'reward_status' => (!empty($result['reward_status']))?$result['reward_status']:CONTENT_ACTIVE,
                          'total_circlet' => (!empty($result['total_circlet']))?$result['total_circlet']: 0,
                          'reward_unlock_time' => (!empty($result['reward_unlock_time']))?$result['reward_unlock_time']: 0,
                          'reward_status_message' => "1- Not claimed; 2.Processing; 3.Can claim.",
                         );

    $this->setResponse('SUCCESS');
    return ($formatResult);
  }
}
