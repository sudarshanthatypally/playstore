<?php
/**
 * Author : Abhijth Shetty
 * Date   : 04-01-2018
 * Desc   : This is a controller file for userLinkAccount Action
 */
class userLinkAccountAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=user.linkAccount", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="type", name="type", description="The type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="account_id", name="account_id", description="The account_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="force_link", name="force_link", description="The force_link specific to this event",
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
    print_log(date('d-m-y h:i:s')."::user.linkAccount");
    print_log("---------------------------------------------------------------------------------------------------------------------");
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $facebookLib = autoload::loadLibrary('utilityLib', 'facebook');

    $result = array();

    if($this->type == FACEBOOK_ACCOUNT && $this->accountId != "")
    {
      //get facebook Id from facebook accesstoken
      $this->accountId = $facebookLib->getFacebookIdFromAccessToken($this->accountId);
      if($this->accountId == 0)
      {
        $this->setResponse('CUSTOM_ERROR', array('error'=>'facebookAccessToken Invalid'));
        return new ArrayObject();
      }

      //check facebookId in DB or not
      $facebookUser = $userLib->checkFacebookId($this->accountId);

      if(empty($facebookUser))
      {
        //add facebookId if not exist
        $userLib->updateUser($this->userId, array("facebook_id" => $this->accountId));
        $userId  = $this->userId;
      }

      //return facebookID if already exist for the same user
      if(!empty($facebookUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId != $facebookUser['user_id'])
      {
        $this->setResponse('CUSTOM_ERROR', array('error'=>'user already exists. do you want switch the account?'));
        $userId = $facebookUser['user_id'];
        $user = $userLib->getUserDetail($userId);
        return array('user_id' => $user['user_id'], 'access_token' => $user['access_token']);
      }

      //return facebookID if already exist for the same user
      if(!empty($facebookUser) && $this->forceLink == CONTENT_INACTIVE && $this->userId == $facebookUser['user_id']){
        $userId  = $this->userId;
      }

      //return facebookID and make force_link
      if(!empty($facebookUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId != $facebookUser['user_id'])
      {
        $user = $userLib->getUserDetail($this->userId);
        $accessToken = md5(md5(rand(11111, 555555)).md5(time()));
        $userLib->updateUser($facebookUser['user_id'], array("device_token"=>$user['device_token'], "access_token" => $accessToken));
        $userLib->updateUser($this->userId, array("device_token"=>$this->userId));
        $userId  = $facebookUser['user_id'];
      }

      //return userid if already exist for the same user
      if(!empty($facebookUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId == $facebookUser['user_id']){
        $userId  = $this->userId;
      }

      if(!empty($facebookUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId == $facebookUser['user_id']){
        $userId  = $this->userId;
      }
    }

    if($this->type == GOOGLE_ACCOUNT && $this->accountId != "")
    {
      //check googleId in DB or not
      $googleUser = $userLib->getUserForGoogleId($this->accountId);

      if(empty($googleUser))
      {
        print_log("::empty google user");
        //add googleId if not exist
        $userProf = $userLib->getUserDetail($this->userId);
        if(empty($userProf['google_id'])){
          $userLib->updateUser($this->userId, array('google_id' => $this->accountId, 'is_login'=> 1));
        }else{
          $this->setResponse('G_USER_ALREADY_EXISTS');
          return array('user_id' => $userProf['user_id'], 'access_token' => $userProf['access_token'], 'google_id'=>$userProf['google_id']);
        }
        if($userProf['device_token']!=($userProf['device_token']."_".$this->userId)){
          $userLib->updateUser($this->userId, array('device_token'=>$userProf['device_token']."_".$this->userId));
        }
        $userId  = $this->userId;
      }

      //return googleId if already exist for the same user
      if(!empty($googleUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId != $googleUser['user_id'])
      {
        print_log("::not empty guser nd userid not matched with guserid");
        //$this->setResponse('CUSTOM_ERROR', array('error'=>'user already exists. do you want switch the account?'));
        $userId = $googleUser['user_id'];
        //$userLib->updateUser($userId, array("is_alert" => 1));
        $user = $userLib->getUserDetail($userId);
        //$userLib->updateUser($this->userId, array("device_token"=>$this->userId));
        //return array('user_id' => $user['user_id'], 'access_token' => $user['access_token']);//,"is_alert"=>1
        $userReq = $userLib->getUserDetail($this->userId);

        $is_alert=$userLib->getUserSameLoginDetailWithPlatform($user['device_token'],1);
        foreach($is_alert as $ia){
          if(!empty($ia['google_id'])){
            $isAlertId= $ia['user_id'];
            $isAlertName= !empty($ia['name'])?$ia['name']:"Guest_".$ia['user_id'];
            $isAlertLevel=$ia['level_id'];
          }
        }
        //$user1= !empty($is_alert['name'])?$is_alert['name']:"Guest_".$is_alert['user_id'];
        if(!empty($userReq['google_id'])){
          $msg = "Do you want to load ".$isAlertName."'s progress with user level ".$isAlertLevel."?";
          $isDel=0;
        }else{
          $msg = "Do you want to load ".$isAlertName."'s progress with user level ".$isAlertLevel."? Progress in the current game will be loss";//".$user2."
          $isDel=1;
        }
        
        $this->setResponse('SUCCESS');
        return array('user_id' => $userId, 'access_token' => $user['access_token'], 'message'=>$msg, 'is_alert'=>1, 'google_id'=> $googleUser['google_id'],'is_delete'=>$isDel);
      }

      //return googleId if already exist for the same user
      if(!empty($googleUser) && $this->forceLink == CONTENT_INACTIVE && $this->userId == $googleUser['user_id']){
        print_log("3");
        $userId  = $this->userId;
        $userLib->updateUser($this->userId, array('is_login'=> 1));
      }

      //return googleId and make force_link
      if(!empty($googleUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId != $googleUser['user_id'])
      {
        print_log("2::not empty guser nd userid not matched with guserid");
        $user = $userLib->getUserDetail($this->userId);
        $accessToken = md5(md5(rand(11111, 555555)).md5(time()));
        $userLib->updateUser($googleUser['user_id'], array("device_token"=>$user['device_token'],"access_token" => $accessToken));
        //$userLib->updateUser($this->userId, array("device_token"=>$this->userId));
        $userId  = $googleUser['user_id'];

        $is_alert=$userLib->getUserSameLoginDetailWithPlatform($user['device_token'],1);
        foreach($is_alert as $ia){
          if(!empty($ia['google_id'])){
            $isAlertId= $ia['user_id'];
            $isAlertName= !empty($ia['name'])?$ia['name']:"Guest_".$ia['user_id'];
            $isAlertLevel=$ia['level_id'];
          }
        }
        //$user1= !empty($is_alert['name'])?$is_alert['name']:"Guest_".$is_alert['user_id'];
        $msg = "Do you want to load ".$isAlertName."'s progress with user level ".$isAlertLevel."?";//".$user2."
        $this->setResponse('SUCCESS');
        return array('user_id' => $userId, 'access_token' => $user['access_token'], 'message'=>$msg, 'is_alert'=>1, 'google_id'=> $googleUser['google_id']);
      }

      //return userid if already exist for the same user
      if(!empty($googleUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId == $googleUser['user_id']){
        print_log("5");
        $userId  = $this->userId;
        $userLib->updateUser($this->userId, array('is_login'=> 1));
      }

      if(!empty($googleUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId == $googleUser['user_id']){
        print_log("6");
        $userId  = $this->userId;
        $userLib->updateUser($this->userId, array('is_login'=> 1));
      }
    }

    if($this->type == GAME_CENTER && $this->accountId != "")
    {
      //check gameCenterId in DB or not
      $gameCenterUser = $userLib->getUserForGameCenterId($this->accountId);
      if(empty($gameCenterUser))
      {
        $userProf = $userLib->getUserDetail($this->userId);
        if(empty($userProf['game_center_id'])){
          $userLib->updateUser($this->userId, array('game_center_id' => $this->accountId, 'is_login'=> 1));
        }else{
          $this->setResponse('G_USER_ALREADY_EXISTS');
          return array('user_id' => $userProf['user_id'], 'access_token' => $userProf['access_token'], 'game_center_id'=>$userProf['game_center_id']);
        }
        if($userProf['device_token']!=($userProf['device_token']."_".$this->userId)){
          $userLib->updateUser($this->userId, array('device_token'=>$userProf['device_token']."_".$this->userId));
        }
        //add gameCenterId if not exist
        //$userLib->updateUser($this->userId, array("game_center_id" => $this->accountId));
        $userId  = $this->userId;
      }

      //return gameCenterId if already exist for the same user
      if(!empty($gameCenterUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId != $gameCenterUser['user_id'])
      {
        $this->setResponse('CUSTOM_ERROR', array('error'=>'user already exists. do you want switch the account?'));
        $userId = $gameCenterUser['user_id'];
        $user = $userLib->getUserDetail($userId);
        $userReq = $userLib->getUserDetail($this->userId);
        $is_alert=$userLib->getUserSameLoginDetailWithPlatform($user['device_token'],2);
        foreach($is_alert as $ia){
          if(!empty($ia['game_center_id'])){
            $isAlertId= $ia['user_id'];
            $isAlertName= !empty($ia['name'])?$ia['name']:"Guest_".$ia['user_id'];
            $isAlertLevel=$ia['level_id'];
          }
        }
        if(!empty($userReq['game_center_id'])){
          $msg = "Do you want to load ".$isAlertName."'s progress with user level ".$isAlertLevel."?";
          $isDel=0;
        }else{
          $msg = "Do you want to load ".$isAlertName."'s progress with user level ".$isAlertLevel."? Progress in the current game will be loss";//".$user2."
          $isDel=1;
        }
        $this->setResponse('SUCCESS');
        return array('user_id' => $userId, 'access_token' => $user['access_token'], 'message'=>$msg, 'is_alert'=>1, 'game_center_id'=> $gameCenterUser['game_center_id'],'is_delete'=>$isDel);
      }

      //return gameCenterId if already exist for the same user
      if(!empty($gameCenterUser) && $this->forceLink == CONTENT_INACTIVE && $this->userId == $gameCenterUser['user_id']){
        $userId  = $this->userId;
        $userLib->updateUser($this->userId, array('is_login'=> 1));
      }

      //return googleId and make force_link
      if(!empty($gameCenterUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId != $gameCenterUser['user_id'])
      {
        $user = $userLib->getUserDetail($this->userId);
        $accessToken = md5(md5(rand(11111, 555555)).md5(time()));
        $userLib->updateUser($gameCenterUser['user_id'], array("device_token"=>$user['device_token'], "access_token" => $accessToken));
        //$userLib->updateUser($this->userId, array("device_token"=>$this->userId));
        $userId  = $gameCenterUser['user_id'];
        $is_alert=$userLib->getUserSameLoginDetailWithPlatform($user['device_token'],2);
        foreach($is_alert as $ia){
          if(!empty($ia['game_center_id'])){
            $isAlertId= $ia['user_id'];
            $isAlertName= !empty($ia['name'])?$ia['name']:"Guest_".$ia['user_id'];
            $isAlertLevel=$ia['level_id'];
          }
        }
        //$user1= !empty($is_alert['name'])?$is_alert['name']:"Guest_".$is_alert['user_id'];
        $msg = "Do you want to load ".$isAlertName."'s progress with user level ".$isAlertLevel."?";//".$user2."
        $this->setResponse('SUCCESS');
        return array('user_id' => $userId, 'access_token' => $user['access_token'], 'message'=>$msg, 'is_alert'=>1, 'game_center_id'=> $gameCenterUser['game_center_id']);
      }

      //return userid if already exist for the same user
      if(!empty($gameCenterUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId == $gameCenterUser['user_id']){
        $userId  = $this->userId;
        $userLib->updateUser($this->userId, array('is_login'=> 1));
      }

      if(!empty($gameCenterUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId == $gameCenterUser['user_id']){
        $userId  = $this->userId;
        $userLib->updateUser($this->userId, array('is_login'=> 1));
      }
    }

    $user = $userLib->getUserDetail($userId);
    print_log("---------------------------------------------------------------------------------------------------------------------");
    $this->setResponse('SUCCESS');
    if(!empty($user['google_id'])){
      return array('user_id' => $user['user_id'], 'access_token' => $user['access_token'],'google_id'=>$user['google_id']);
    }elseif(!empty($user['game_center_id'])){
      return array('user_id' => $user['user_id'], 'access_token' => $user['access_token'],'game_center_id'=>$user['game_center_id']);
    }else{
      return array('user_id' => $user['user_id'], 'access_token' => $user['access_token']);
    }
    
  }
}
