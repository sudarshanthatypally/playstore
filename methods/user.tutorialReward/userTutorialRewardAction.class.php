<?php
/**
 * Author : Abhijth Shetty
 * Date   : 06-01-2018
 * Desc   : This is a controller file for roomSaveResult Action
 */
class userTutorialRewardAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=room.saveResult", tags={"Rewards"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="tutorial_status", name="tutorial_status", description="The tutorial_status specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="win_status", name="win_status", description="The win_status specific to this event",
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
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    $cubeLib = autoload::loadLibrary('queryLib', 'cube');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    $badgeLib = autoload::loadLibrary('queryLib', 'badge');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $deckLib = autoload::loadLibrary('queryLib', 'deck');

    $result = array();
    $cubeBonus = new ArrayObject();
    $responseFormat  = new ArrayObject();
    $slotList = array(1, 2, 3, 4);

    $user = $userLib->getUserDetail($this->userId);

    if($this->winStatus != BATTLE_DRAW_STATUS && $this->winStatus != BATTLE_WON_STATUS && $this->winStatus != BATTLE_LOST_STATUS)
    {
      $this->setResponse('CUSTOM_ERROR', array('error'=>'invalid option'));
      return new ArrayObject();
    }
    $matchStatusReward = $rewardLib->getMasterMatchStatusRewardForStadium($this->winStatus, $user['master_stadium_id']);
    
    if(empty($matchStatusReward) || $matchStatusReward == ""){
      $mSId=$rewardLib->getMaxStadiumIdMasterMatchStatusRewardForStadium();
      $maxStadiumId=$mSId['master_stadium_id'];
      if(empty($maxStadiumId) || $maxStadiumId==""){
        $maxStadiumId=5;
      }
      $matchStatusReward = $rewardLib->getMasterMatchStatusRewardForStadium($this->winStatus, $maxStadiumId);
    }
    $userParamList['gold'] = $user['gold'] + $matchStatusReward['gold'];
    //$userParamList['crystal'] =$user['crystal'];
    $userParamList['crystal'] =DEFAULT_CRYSTAL;
    $userLib->updateUser($this->userId, $userParamList);

    $cubeBonus['cube_id'] = 1;
    $cubeBonus['seq_id']=$user['seq_id'];
    $cubeBonus['seq_pos_id']= 1;
    $userRewardList = $cubeLib->CheckEligibilityOfCubeReward($this->userId);

    if(count($userRewardList) < MIN_CUBE_REWARD)
    {
      foreach($userRewardList as $reward){
        $slotsFilled[] = $reward['slot_id'];
      }
    }
    $cubeBonus['is_lapsed'] = (count($userRewardList)>= MIN_CUBE_REWARD)?true:false;

    //if user rewarded with a cube then add to user_reward
    if(!($cubeBonus['is_lapsed']) && !empty($cubeBonus['cube_id']))
    {
      $cube = $userLib->getMasterCubeRewardForStadium($cubeBonus['cube_id'], $user['master_stadium_id']);
      $freeSlot = (array_diff($slotList, $slotsFilled));

      $userLib->insertUserReward(array(
                  'user_id' => $this->userId,
                  'seq_id' => $cubeBonus['seq_id'],
                  'seq_pos_id' => $cubeBonus['seq_pos_id'],
                  'cube_id' => $cubeBonus['cube_id'],
                  'slot_id' => empty($freeSlot)?1:array_pop(array_reverse($freeSlot)),
                  'master_stadium_id' => $user['master_stadium_id'],
                  'created_at' => date('Y-m-d H:i:s'),
                  'status' => CUBE_ACTIVE));

    }  

    $responseFormat = array('win_status' => $this->winStatus,
                        'master_stadium_id' => $user['master_stadium_id'],
                        'total_gold' => (empty($userParamList['gold'])?$user['gold']:$userParamList['gold']),
                        'gold_bonus' => $matchStatusReward['gold'],
                        'total_crystal' => (empty($userParamList['crystal'])?$user['crystal']:$userParamList['crystal']),
                        'crystal_bonus' => DEFAULT_CRYSTAL,
                        'cube_bonus' =>  (empty($cubeBonus['cube_id'])?"":$cubeBonus)
                      );

    $this->setResponse('SUCCESS');
    return $responseFormat;
  }
}
