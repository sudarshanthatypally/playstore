<?php
/**
 * Author : Abhijth Shetty
 * Date   : 31-01-2018
 * Desc   : This is a controller file for inAppPurchaseUpdate Action
 */
class inAppPurchaseUpdateAction extends baseAction{
	 /**
   * @OA\Get(path="?methodName=inAppPurchase.update", tags={"Purchase"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="crystal", name="crystal", description="The crystal specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="data", name="data", description="The data specific to this event",
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
    print_log("inAppPurchaseUpdate Api");
    print_log("------------------------------------------------------------------------------------------------------------------");
	  
    $inAppPurchaseLib = autoload::loadLibrary('queryLib', 'inAppPurchase');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $reward = autoload::loadLibrary('queryLib', 'reward');

    $result = array();
    $user = $userLib->getUserDetail($this->userId);
    print_log($this->userId);
    if(empty($this->data))
    {
      $this->setResponse('INSUFFICIENT_BALANCE');
      return new ArrayObject();
    }

    $userLib->updateUser($this->userId, array('crystal' => $user['crystal'] + $this->crystal));
    $user = $userLib->getUserDetail($this->userId);

    $inAppPurchaseLib->insertInAppPurchase(array('user_id' => $this->userId,
                              'crystal' => $this->crystal,
                              'data' => $this->data,
                              'created_at' =>  date('Y-m-d H:i:s'),
                              'status' => CONTENT_ACTIVE));
    
    print_log($this->crystal);
    print_log($this->data);
    $dataVal= array('user_id' => $this->userId,
                              'crystal' => $this->crystal,
                              'data' => $this->data,
                              'created_at' =>  date('Y-m-d H:i:s'),
                              'status' => CONTENT_ACTIVE);
    $inAppPurchaseLib->insertInAppPurchaseInventory(array('user_id' => $this->userId,
                                                              'data' => json_encode($dataVal),
                                                              'created_at' =>  date('Y-m-d H:i:s')));
    $result['total_crystal'] = $user['crystal'];
    $result['crystal_bonus'] = $this->crystal;
    print_log("------------------------------------------------------------------------------------------------------------------");
    $this->setResponse('SUCCESS');
    return $result;
  }
}
