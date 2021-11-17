<?php
/**
 * Author : Abhijth Shetty
 * Date   : 06-02-2018
 * Desc   : This is a controller file for cubeList Action
 */
class cubeListAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=cube.list", tags={"Cubes"}, 
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
    $inAppPurchaseLib = autoload::loadLibrary('queryLib', 'inAppPurchase');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $reward = autoload::loadLibrary('queryLib', 'reward');
    $cubeLib = autoload::loadLibrary('queryLib', 'cube');

    //Based on user current stadium cubeLists are given.
    $result = array();
    $user = $userLib->getUserDetail($this->userId);
    $cubeInventoryList = $inAppPurchaseLib->getMasterCubeInventoryListBasedOnStadium($user['master_stadium_id']);
    if($user['master_stadium_id']==1){
      $cubeInventoryList = $inAppPurchaseLib->getMasterCubeInventoryListBasedOnStadium(2);
    }
    foreach ($cubeInventoryList as $cubeInventory)
    {
      $temp = array();
      $temp['master_cube_inventory_id'] = $cubeInventory['master_cube_inventory_id'];
      $temp['cube_id'] = $cubeInventory['cube_id'];
      $temp['master_stadium_id'] = $cubeInventory['master_stadium_id'];
      $temp['required_crystal'] = $cubeInventory['amount'];
      $cubeReward = $cubeLib->getCubeRewardDetailForStadium($cubeInventory['cube_id'], $user['master_stadium_id']);
      $temp['gold_bonus'] = $cubeReward['gold'];
      $temp['card_count'] = $cubeReward['card_count'];
      //getting possible card range of each type
      $temp['common_count'] =$cubeReward['common'];
      $temp['rare_count']=$cubeReward['rare'];
      $temp['epic_count']=$cubeReward['epic'];
      $temp['ultra_epic_count']=$cubeReward['ultra_epic'];
      //$temp['common_count'] = MIN_CARD_COUNT." - ".($cubeReward['card_count'] - $cubeReward['rare'] - $cubeReward['epic'] - $cubeReward['ultra_epic']);
      //$temp['rare_count'] = $cubeReward['rare']." - ".($cubeReward['card_count'] - $cubeReward['rare'] - $cubeReward['epic'] - $cubeReward['ultra_epic']);
      //$temp['epic_count'] = $cubeReward['epic']." - ".($cubeReward['card_count'] - $cubeReward['rare'] - $cubeReward['epic'] -$cubeReward['ultra_epic']);
      //$temp['ultra_epic_count'] = $cubeReward['ultra_epic']." - ".($cubeReward['card_count'] - $cubeReward['rare'] - $cubeReward['epic'] - $cubeReward['ultra_epic']);
      $result[] = $temp;
    }

    $this->setResponse('SUCCESS');
    return array('inventory_list' => $result, 'cube_id_message' => "1-Titanium; 2- Diamond; 3- Platinum");
  }
}
