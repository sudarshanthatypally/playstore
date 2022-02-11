<?php
/**
 * Author : Abhijth Shetty
 * Date   : 28-12-2017
 * Desc   : This is a controller file for userUpdate Action
 */
class userUpdateAction extends baseAction{
	  /**
   * @OA\Get(path="?methodName=user.update", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="name", name="name", description="The name specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="level_id", name="level_id", description="The level_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="ios_push_token", name="ios_push_token", description="The ios_push_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="android_push_token", name="android_push_token", description="The android_push_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="notification_status", name="notification_status", description="The notification_status specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="is_tutorial_completed", name="is_tutorial_completed", description="The is_tutorial_completed specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="editname_count", name="editname_count", description="The editname_count specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingQueen_status", name="kingQueen_status", description="The kingQueen_status specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="isKathikaTutorial", name="isKathikaTutorial", description="The isKathikaTutorial specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="isStorybookTutorial", name="isStorybookTutorial", description="The isStorybookTutorial specific to this event",
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
    $result = $result['achievement'] = new ArrayObject();
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    $cardLib = autoload::loadLibrary('queryLib', 'card');

    $paramList =  $result['achievement'] = array();

    $userDetail = $userLib->getUserDetail($this->userId);

    if($this->name != "")
    {
      $paramList['name'] = $this->name;
    }

    if($this->levelId != "")
    {
      $paramList['level_id'] = $this->levelId;
    }

    if($this->isTutorialCompleted != "")
    {
      if($userDetail['is_tutorial_completed'] == CONTENT_INACTIVE && $this->isTutorialCompleted == CONTENT_ACTIVE)
      {
        $paramList['is_tutorial_completed'] = $this->isTutorialCompleted;
        $result['is_tutorial_completed'] = $this->isTutorialCompleted;
        /*$masterAchievement = $achievementLib->getMasterAchievementDetailForType(ACHIEVEMENT_TYPE_TUTORIAL_COMPLETED);
        $userAchivement = $achievementLib->getUserAchievementListForAchievementId($this->userId, $masterAchievement['master_achievement_id']);
        if(empty($userAchivement))
        {
          $achievementLib->insertUserAchievement(array('user_id' => $this->userId,
                             'master_achievement_id' => $masterAchievement['master_achievement_id'],
                             'created_at' => date('Y-m-d H:i:s'),
                             'status' => CONTENT_ACTIVE));

          $paramList['xp'] = $userDetail['xp'] + $masterAchievement['xp'];
          $result['achievement'][]['master_achievement_id'] = $masterAchievement['master_achievement_id'];
        }*/
        // $paramList['master_stadium_id'] = $userDetail['master_stadium_id'] = MONKEY_STADIUM;
        // $cardLib->cardUnlock($this->userId, $paramList['master_stadium_id']);
      }
    }
    // $result['master_stadium_id'] = $userDetail['master_stadium_id'];
    if($this->tutorial_seq != "")
    {
      $paramList['tutorial_seq'] = $this->tutorial_seq+1;
      if($this->tutorial_seq==3){
        $paramList['is_tutorial_completed'] =1;
        $result['is_tutorial_completed'] = $paramList['is_tutorial_completed'];
      }else{
        $result['is_tutorial_completed'] = 2;
      }
      $result['tutorial_seq']=$this->tutorial_seq+1;
    }
    if($this->notificationStatus != "")
    {
      $paramList['notification_status'] = $this->notificationStatus;
    }

    if($this->androidPushToken != "")
    {
      $paramList['android_push_token'] = $this->androidPushToken;
    }

    if($this->iosPushToken != "")
    {
      $paramList['ios_push_token'] = $this->iosPushToken;
    }
    if($this->kingQueen_status != "")
    {
      $paramList['kingQueen_status'] = $this->kingQueen_status;
    }
    if($this->isLogin != "")
    {
      $paramList['is_login'] = $this->isLogin;
    }
    if($this->IOS_update != "")
    {
      $paramList['IOS_update'] = $this->IOS_update;
    }
    if($this->android_update != "")
    {
      $paramList['android_update'] = $this->android_update;
    }
    if($this->isKathikaTutorial != "" && $this->isKathikaTutorial==1)
    {
      $paramList['is_kathika_tutorial_completed'] = $this->isKathikaTutorial;
    }
    if($this->isStorybookTutorial != "" && $this->isStorybookTutorial==1)
    {
      $paramList['is_storybook_tutorial_completed'] = $this->isStorybookTutorial;
    }
    if($this->editNameCost != "")
    {
      $dt=date('Y-m-d H:i:s');
      $enc = $userLib->getEditNameClaimed($this->userId,$dt);
      date_default_timezone_set('Asia/Kolkata');
      if(!empty($enc)){
        $dt=date('Y-m-d H:i:s');
        $nextdt=date('Y-m-d H:i:s',strtotime('+1 day', strtotime($enc['time'])));
        $seconds = strtotime($nextdt) - strtotime($dt);
        $hours = floor($seconds / 3600);
        $seconds %= 3600;
        $minutes = floor($seconds / 60);
        $seconds %= 60;
        $result['msg_show'] = "You have successfully unlocked name change for once. You need to wait another ";
        if($hours!=0){
          if($hours<=1){
            $result['msg_show'] .= "$hours hr ";
          }else{
            $result['msg_show'] .= "$hours hrs ";
          }
        }else{
            if($minutes!=0){
              $result['msg_show'] .= " $minutes min ";
            }else{
              if($seconds!=0){
                $result['msg_show'] .= "$seconds sec ";
              }
            } 
        }    
        $result['msg_show'].='to change your name again!';
        $result['isreadytochange']=0;
        $result['nextdt']= $nextdt;
        $result['dt']= $dt;
        $secondss = strtotime($nextdt) - strtotime($dt);
        $result['secondss']= $secondss;
        //$result['msg_show']='You have successfully unlocked name change for once. You need to wait another '.$dt.'h to change your name again!';
      }else{
        $editNameId = $userLib->insertEditName(array(
          'name' => $this->name,
          'time' => date('Y-m-d H:i:s'),
          'user_id' => $this->userId,
          'status' => CONTENT_ACTIVE));
        $paramList['crystal'] =$userDetail['crystal']-$this->editNameCost;
        $userDetail = $userLib->getUserDetail($this->userId);
        $result['isreadytochange']=1;
        $result['total_crystal']=$paramList['crystal'];
        $result['msg_show']='You have successfully unlocked name change for once. You need to wait another 24h to change your name again!';
        if($this->editName_count != "")
        {
          $paramList['editname_count'] = $this->editName_count;
        }
      }
    }else{
      if($this->editName_count != "")
        {
          $paramList['editname_count'] = $this->editName_count;
        }
    }
    if(!empty($paramList)){
      $userLib->updateUser($this->userId, $paramList);
    }

    $this->setResponse('SUCCESS');
    return $result;
  }
}
