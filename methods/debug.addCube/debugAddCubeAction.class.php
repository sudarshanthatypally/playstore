<?php
/**
 * Author : Abhijth Shetty
 * Date   : 11-11-2019
 * Desc   : This is a controller file for debugAddCube Action 
 */
class debugAddCubeAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=debug.addCube", tags={"Debug"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="cube_type", name="cube_type", description="The cube_type specific to this event",
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
    $cubeLib = autoload::loadLibrary('queryLib', 'cube');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $result = $slotsFilled = array();
    $slotList = array(1, 2, 3, 4);

    $user = $userLib->getUserDetail($this->userId);
    $userRewardList = $cubeLib->CheckEligibilityOfCubeReward($this->userId);

    if(count($userRewardList) >= MIN_CUBE_REWARD)
    {
      $this->setResponse('CUSTOM_ERROR', array('error'=>'No slot available'));
      return new ArrayObject();
    }


    foreach($userRewardList as $reward){
      $slotsFilled[] = $reward['slot_id'];
    }

    $freeSlot = (array_diff($slotList, $slotsFilled));

    $userLib->insertUserReward(array(
      'user_id' => $this->userId,
      'cube_id' => $this->cubeType,
      'slot_id' => empty($freeSlot)?1:array_pop(array_reverse($freeSlot)),
      'master_stadium_id' => $user['master_stadium_id'],
      'created_at' => date('Y-m-d H:i:s'),
      'status' => CUBE_ACTIVE));

    $this->setResponse('SUCCESS');
    return $result;
  }  
}