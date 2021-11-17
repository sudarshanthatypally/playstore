<?php
/**
 * Author : Abhijth Shetty
 * Date   : 04-10-2019
 * Desc   : This is a controller file for inviteCreate Action 
 */
class inviteCreateAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=invite.create", tags={"Invite"}, 
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
    $inviteLib = autoload::loadLibrary('queryLib', 'invite');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $result = array();
    date_default_timezone_set('Asia/Kolkata');
    $userDetail = $userLib->getUserDetail($this->userId); 
    //add 5 link per hour limitation
    $userInvites = $inviteLib->getInviteListWithLimit($this->userId, MAX_INVITE_PER_HOUR);
    if(sizeof($userInvites)==MAX_INVITE_PER_HOUR && strtotime($userInvites[MAX_INVITE_PER_HOUR-1]['created_at']) > time()-3600){
      $result['next_invite'] = (strtotime($userInvites[MAX_INVITE_PER_HOUR-1]['created_at'])+3600)-time();
      $this->setResponse('MAX_INVITE_LIMIT_REACHED');
      return $result;
    }
    //$accessToken = $this->access_token;
    $accessToken = (isset($userDetail['access_token']) ? $userDetail['access_token'] : false);
    $inviteToken = md5(md5($this->userId).md5($accessToken).md5(time()));
    $inviteLib->insertInvite(array('user_id'=>$this->userId, 
      'invite_token'=>$inviteToken,
      'status'=>CONTENT_ACTIVE,
      'created_at'=>date('Y-m-d H:i:s')));

      if(!empty($userDetail['name'])){
        $userName=$userDetail['name'];
      }else{
        $userName="Guest ".$this->userId; 
      }
    $result['referrer_name'] = $userName;
    $result['invite_token'] = $inviteToken;
    $this->setResponse('SUCCESS');
    return $result;
  }  
}