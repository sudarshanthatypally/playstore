<?php
/**
 * Author : Abhijth Shetty
 * Date   : 06-02-2018
 * Desc   : This is a controller file for cubeUpdate Action
 */
class cubeUpdateAction extends baseAction{
	 /**
   * @OA\Get(path="?methodName=cube.update", tags={"Cubes"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="master_cube_inventory_id", name="master_cube_inventory_id", description="The master_cube_inventory_id specific to this event",
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
    $inAppPurchaseLib = autoload::loadLibrary('queryLib', 'inAppPurchase');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cubeLib = autoload::loadLibrary('queryLib', 'cube');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');

    $result = $cardIdList = $cardList = array();
    $cardCount = 0;

    $user = $userLib->getUserDetail($this->userId);
    $cubeInventoryDetail = $inAppPurchaseLib->getMasterCubeInventoryDetail($this->masterCubeInventoryId);

    if($user['crystal'] < $cubeInventoryDetail['amount'])
    {
      $this->setResponse('CRYSTAL_IS_NOT_ENOUGH');
      return new ArrayObject();
    }
 
    $cubeRewardDetail = $cubeLib->getCubeRewardDetailForStadium($cubeInventoryDetail['cube_id'], $user['master_stadium_id']);
    $cardIdList = $rewardLib->getRandomCard($cubeRewardDetail, $user['master_stadium_id']);
    print_log($cardIdList);
   /* for($i=0;$i<count($cardIdList);$i++){
      $cardList[] = $rewardLib->addRewardedCard($this->userId, $cardIdList[$i],DEFAULT_CARD_COUNT);
    }*/
    foreach($cardIdList as $cardId => $cardIdVal){
      $cardList[] = $rewardLib->addRewardedCard($this->userId, $cardId, $cardIdVal);
    }
    //print_log($cardList);
    $result['card_details'] = $cardList;
    $result['master_stadium_id'] = $user['master_stadium_id'];
    $result['gold_bonus'] = $cubeRewardDetail['gold'];
    $result['card_count'] = $cubeRewardDetail['card_count'];
    //$result['rare_count'] = $result['epic_count'] = $result['ultra_epic_count'] = 0;
    $result['cube_id'] = $cubeInventoryDetail['cube_id'];
    if($cubeRewardDetail['common'] > 0){
      $result['common_count'] = $cubeRewardDetail['common'];
    }
    if($cubeRewardDetail['rare'] > 0){
      $result['rare_count'] = $cubeRewardDetail['rare'];
    }

    if($cubeRewardDetail['epic'] > 0){
      $result['epic_count'] = $cubeRewardDetail['epic'];
    }

    if($cubeRewardDetail['ultra_epic'] > 0){
      $result['ultra_epic_count'] = $cubeRewardDetail['ultra_epic'];
    }

    print_log("=========================== cubeUpdateAction =================================");
    print_log("user crystal::".$user['crystal']);
    print_log("character crystal amount::".$cubeInventoryDetail['amount']);
    print_log("=========================== cubeUpdateAction END =================================");
    
    //update the userDetail :deduct crystal/add gold according to the cube user purchased
    $userLib->updateUser($this->userId, array('crystal' => $user['crystal'] - $cubeInventoryDetail['amount'],
                          'gold' => $user['gold'] + $cubeRewardDetail['gold']));
    $user = $userLib->getUserDetail($this->userId);

    $result['total_crystal'] = $user['crystal'];
    $result['total_gold'] = $user['gold'];
    $result['required_crystal'] = $cubeInventoryDetail['amount'];

    $this->setResponse('SUCCESS');
    return $result;
  }
}
