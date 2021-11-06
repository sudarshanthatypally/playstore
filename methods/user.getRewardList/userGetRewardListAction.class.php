<?php
/**
 * Author : Abhijth Shetty
 * Date   : 09-01-2018
 * Desc   : This is a controller file for userGetRewardList Action
 */
class userGetRewardListAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=user.getRewardList", tags={"Users"}, 
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

    //returns the list of rewards
    $userRewardList = $userLib->getUserRewardActiveList($this->userId, CONTENT_CLOSED);

    if($this->userRewardtype == CUBE_EARNED_DURING_MATCH){
      $userRewardList = $userLib->getUserRewardsActiveList($this->userId, CONTENT_CLOSED);
    }

    foreach($userRewardList as $reward)
    {
      $temp = array();
      /*$maxTime = ($reward['cube_id'] == CUBE_FIRECRACKER)?UNLOCK_CUBE_FIRECRACKER_TIMEOUT:(($reward['cube_id'] == CUBE_BOMB)?UNLOCK_CUBE_BOMB_TIMEOUT:(($reward['cube_id'] == CUBE_METALBOMB) ? UNLOCK_CUBE_METALBOMB_TIMEOUT : UNLOCK_CUBE_ROCKET_TIMEOUT));
      */
      switch ($reward['cube_id']) {
      case CUBE_FIRECRACKER:
       $mTime = UNLOCK_CUBE_FIRECRACKER_TIMEOUT;
        break;
      case CUBE_BOMB:
        $mTime = UNLOCK_CUBE_BOMB_TIMEOUT;
        break;
      case CUBE_ROCKET:
        $mTime = UNLOCK_CUBE_ROCKET_TIMEOUT;
        break;
      case CUBE_DYNAMITE:
        $mTime = UNLOCK_CUBE_DYNAMITE_TIMEOUT;
        break;
      case CUBE_METALBOMB:
        $mTime = UNLOCK_CUBE_METALBOMB_TIMEOUT;
        break;
      default:
        break;
    }
    $maxTime = $mTime;
      $temp['reward_time'] = $maxTime;
      $temp_time= strtotime(date("Y-m-d H:i:s", strtotime('+'.$maxTime.' hours', $reward['claimed_at'])));
      $temp['reward_unlock_time'] = (($reward['claimed_at'] == 0) || (($temp_time) - time()<0))?0:(($temp_time) - time());  
      //(($reward['claimed_at'] == 0) || (($reward['claimed_at']+$maxTime) - time()<0))?0:(($reward['claimed_at']+$maxTime) - time());
      //$temp['reward_unlock_time'] = (($reward['claimed_at'] == 0) || (($reward['claimed_at']+$maxTime) - time()<0))?0:(($reward['claimed_at']+$maxTime) - time());
      $temp['slot_id'] = $reward['slot_id'];
      $temp['user_reward_id'] = $reward['user_reward_id'];
      $temp['cube_id'] = $reward['cube_id'];
      $cubeRewardDetail = $cubeLib->getCubeRewardDetailForStadium($reward['cube_id'], $reward['master_stadium_id']);
      $temp['gold_bonus'] = !empty($cubeRewardDetail) ? $cubeRewardDetail['gold'] : 0;
      $temp['crystal_bonus']= $cubeRewardDetail['crystal'];
      $temp['crystal_cost'] = $cubeRewardDetail['crystal_cost'];
      $temp['card_count'] = !empty($cubeRewardDetail) ? $cubeRewardDetail['card_count'] : 0;
      $temp['ultra_epic_count'] = !empty($cubeRewardDetail) ? $cubeRewardDetail['ultra_epic'] : 0;
      $temp['epic_count'] = !empty($cubeRewardDetail) ? $cubeRewardDetail['epic'] : 0;
      $temp['rare_count'] = !empty($cubeRewardDetail) ? $cubeRewardDetail['rare'] : 0;
      $temp['common_count'] = !empty($cubeRewardDetail) ? $cubeRewardDetail['common'] : 0;
      

      $temp['master_stadium_id'] = $reward['master_stadium_id'];
      $temp['reward_status'] = ($temp['reward_unlock_time'] <= 0 && $reward['status'] == CUBE_ON_PROCESS)?CUBE_CAN_BE_CLAIMED:($temp['reward_unlock_time'] <= 0 && $reward['status'] == CUBE_ACTIVE)?CUBE_ACTIVE:$reward['status'];

      //Updating the status of the cube.
      if($temp['reward_unlock_time'] <= 0 && $reward['status'] == CUBE_ON_PROCESS){
        $userLib->updateUserReward($reward['user_reward_id'], array('status' => CUBE_CAN_BE_CLAIMED));
        $temp['reward_status'] = CUBE_CAN_BE_CLAIMED;
      }
      $temp['reward_status_message'] = "1- Not claimed; 2- On process; 3.Can claim ";
      $result[] = $temp;
    }

    $this->setResponse('SUCCESS');
    return array('user_reward_list' => $result);
  }
}
