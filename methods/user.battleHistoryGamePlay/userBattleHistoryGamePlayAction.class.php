<?php
/**
 * Author : Abhijth Shetty
 * Date   : 04-01-2018
 * Desc   : This is a controller file for userLinkAccount Action
 */
class userBattleHistoryGamePlayAction extends baseAction{
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
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $facebookLib = autoload::loadLibrary('utilityLib', 'facebook');

    $result = array();


    $result['file_name'] = $this->game_video;
    $result['room_id'] = $this->roomId;
    $file_upload_message = '';
   /* $file_name = $this->game_video['name'];
    $file_size = $_FILES['game_video']['size'];
    $tmp_file = $_FILES['game_video']['tmp_name'];*/
    $valid_file_formats = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "MP4");
    list($txt, $file_extension) = explode(".", $file_name);
    if ($file_name) {
      if (in_array($file_extension, $valid_file_formats)) {
        $result['file_name'] = $this->game_video;
       // $result['file_size'] = $file_size;
        //$result['tmp_file'] = $tmp_file;
        //$result['valid_file_formats'] = $valid_file_formats;
        $result['room_id'] = $this->roomId;
        
        /*include('config.php');
        $new_image_name = time() . "." . $file_extension;
        if ($s3->putObjectFile($tmp_file, $bucket, $new_image_name, S3::ACL_PUBLIC_READ)) {
          $file_upload_message = "File Uploaded Successfully to amazon S3.<br><br>";
          $uploaded_file_path = 'http://' . $bucket . '.s3.amazonaws.com/' . $new_image_name;
          $file_upload_message .= '<b>Upload File URL:</b>' . $uploaded_file_path . "<br/>";
          $file_upload_message .= "<img src='$uploaded_file_path'/>";
        } else {
          $file_upload_message = "<br>File upload to amazon s3 failed!. Please try again.";
        }*/
      } else {
        $file_upload_message = "<br>This file format is not allowed, please upload only image file.";
      }
    } else {
      $file_upload_message = "<br>Please select image file to upload.";
    }
    
/*
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
        //add googleId if not exist
        $userLib->updateUser($this->userId, array("google_id" => $this->accountId));
        $userId  = $this->userId;
      }

      //return googleId if already exist for the same user
      if(!empty($googleUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId != $googleUser['user_id'])
      {
        $this->setResponse('CUSTOM_ERROR', array('error'=>'user already exists. do you want switch the account?'));
        $userId = $googleUser['user_id'];
        $user = $userLib->getUserDetail($userId);
        return array('user_id' => $user['user_id'], 'access_token' => $user['access_token']);
      }

      //return googleId if already exist for the same user
      if(!empty($googleUser) && $this->forceLink == CONTENT_INACTIVE && $this->userId == $googleUser['user_id']){
        $userId  = $this->userId;
      }

      //return googleId and make force_link
      if(!empty($googleUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId != $googleUser['user_id'])
      {
        $user = $userLib->getUserDetail($this->userId);
        $accessToken = md5(md5(rand(11111, 555555)).md5(time()));
        $userLib->updateUser($googleUser['user_id'], array("device_token"=>$user['device_token'],"access_token" => $accessToken));
        $userLib->updateUser($this->userId, array("device_token"=>$this->userId));
        $userId  = $googleUser['user_id'];
      }

      //return userid if already exist for the same user
      if(!empty($googleUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId == $googleUser['user_id']){
        $userId  = $this->userId;
      }

      if(!empty($googleUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId == $googleUser['user_id']){
        $userId  = $this->userId;
      }
    }

    if($this->type == GAME_CENTER && $this->accountId != "")
    {
      //check gameCenterId in DB or not
      $gameCenterUser = $userLib->getUserForGameCenterId($this->accountId);
      if(empty($gameCenterUser))
      {
        //add gameCenterId if not exist
        $userLib->updateUser($this->userId, array("game_center_id" => $this->accountId));
        $userId  = $this->userId;
      }

      //return gameCenterId if already exist for the same user
      if(!empty($gameCenterUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId != $gameCenterUser['user_id'])
      {
        $this->setResponse('CUSTOM_ERROR', array('error'=>'user already exists. do you want switch the account?'));
        $userId = $gameCenterUser['user_id'];
        $user = $userLib->getUserDetail($userId);
        return array('user_id' => $user['user_id'], 'access_token' => $user['access_token']);
      }

      //return gameCenterId if already exist for the same user
      if(!empty($gameCenterUser) && $this->forceLink == CONTENT_INACTIVE && $this->userId == $gameCenterUser['user_id']){
        $userId  = $this->userId;
      }

      //return googleId and make force_link
      if(!empty($gameCenterUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId != $gameCenterUser['user_id'])
      {
        $user = $userLib->getUserDetail($this->userId);
        // $accessToken = md5(md5(rand(11111, 555555)).md5(time()));
        $userLib->updateUser($gameCenterUser['user_id'], array("device_token"=>$user['device_token'], "access_token" => $accessToken));
        $userLib->updateUser($this->userId, array("device_token"=>$this->userId));
        $userId  = $gameCenterUser['user_id'];
      }

      //return userid if already exist for the same user
      if(!empty($gameCenterUser) && $this->forceLink != CONTENT_ACTIVE && $this->userId == $gameCenterUser['user_id']){
        $userId  = $this->userId;
      }

      if(!empty($gameCenterUser) && $this->forceLink == CONTENT_ACTIVE && $this->userId == $gameCenterUser['user_id']){
        $userId  = $this->userId;
      }
    }

    $user = $userLib->getUserDetail($userId);*/
    $this->setResponse('SUCCESS');
    return $result;
    //return array('user_id' => $user['user_id'], 'access_token' => $user['access_token']);
  }
}
