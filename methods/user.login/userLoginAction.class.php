<?php
/**
 * Author : Abhijth Shetty
 * Date   : 28-12-2017
 * Desc   : This is a controller file for userLogin Action
 */
class userLoginAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=user.login", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="name", name="name", description="The name specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="device_token", name="device_token", description="The device_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="ios_push_token", name="ios_push_token", description="The ios_push_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="android_push_token", name="android_push_token", description="The android_push_token specific to this event",
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
    date_default_timezone_set('Asia/Kolkata');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $deckLib = autoload::loadLibrary('queryLib', 'deck');
    $result = array();

    $accessToken = md5(md5(rand(11111, 555555)).md5(time()));
    if($this->deviceToken == "")
    {
      $this->setResponse('DEVICE_TOKEN_MANDATORY');
      return false;
    }

    if($this->deviceToken != "")
    {
      $deviceUser = $userLib->getUserForDeviceToken($this->deviceToken);
      $nm=empty($this->name)?"GUEST":$this->name;
      if(empty($deviceUser))
      {
        $randVal = rand(7,9);
        $user_uid = $userLib->secure_random_string($randVal);
        $userId = $userLib->insertUser(array(
                    'name' => $this->name,
                    'type' => USER_TYPE_GUEST,
                    'device_token' => $this->deviceToken,
                    'ios_push_token' => $this->iosPushToken,
                    'android_push_token' => $this->androidPushToken,
                    'access_token' => $accessToken,
                    'master_stadium_id' => DEFAULT_STADIUM,
                    'created_at' => date('Y-m-d H:i:s'),
                    'user_uid' => strtoupper($user_uid),
                    'seq_id'=> rand(1,10),
                    'status' => CONTENT_ACTIVE));

        $userLib->processRegistration($userId);
      } else {
        $userId = $deviceUser['user_id'];
      }
    }
 
    if($userId > 0)
    {
      $userDetail = $userLib->getUserDetail($userId);
      $userLib->updateUser($userId, array(
        'access_token' => md5(md5(rand(11111, 555555)).md5(time())),
        'android_push_token' => ($this->androidPushToken=="")?$userDetail['android_push_token']:$this->androidPushToken,
        'ios_push_token' => ($this->iosPushToken=="")?$userDetail['ios_push_token']:$this->iosPushToken
      ));

      $userDetail = $userLib->getUserDetail($userId);

      //------------------------------------- deck -----------------------------
     /* $userDeckLst = $deckLib->getUserDeckDetail($userId);
      if(empty($userDeckLst)){
        $resultDeck = array();
        $DeskList = $cardLib->getUserCardForActiveDeck($userId, DECK_ACTIVE); 
        $deckFLst=array();
        $resultDeck['current_deck_number']=0;
        for($i=0;$i<=3;$i++){
          $deckLst=array();
          $deckLst['deck_id']=$i;
          $j=0;
          if($j<=7){
            $oppdeckList=array();
            foreach ($DeskList as $dcard) 
            {
              $cardPropertyInfo2 = $temp2 = array();
              $temp2['master_card_id'] = $dcard['master_card_id'];
              $oppdeckList[] = $temp2;
              $j++;
            }
          }
          $deckLst['cards']=$oppdeckList;
          $deckFLst[]=$deckLst;
        }
        
        $resultDeck['deck_details']= $deckFLst;
        
        $deckLib->insertUserDeck(array(
          'user_id' => $userId,
          'deck_data' => json_encode($resultDeck),
          'created_at' => date('Y-m-d H:i:s'),
          'status' => CONTENT_ACTIVE
        ));
        
      }*/
      
      //------------------------------------- deck -----------------------------
      $this->setResponse('SUCCESS');
      return array('user_id' => $userId, "access_token" => $userDetail['access_token']);
    }

    $this->setResponse('SUCCESS');
    return $result;
  }
}
