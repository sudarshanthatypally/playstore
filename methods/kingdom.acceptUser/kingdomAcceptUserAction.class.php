<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 09-11-2020
 * Desc   : This is a controller file for kingdomCreate Action
 */
class kingdomAcceptUserAction extends baseAction
{
	/**
   * @OA\Get(path="?methodName=kingdom.acceptUser", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="accept_user_id", name="accept_user_id", description="The accept_user_id specific to this event",
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
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $notificationLib = autoload::loadLibrary('queryLib', 'notification');
    
    $result = array();
    $userList = $requestedUserList = array();
    $userDetails = array();
    $waitngPlayerRoomId = $kingdomId = $roomId = 0;
    //Get the user Detail.
    $user = $userLib->getUserDetail($this->userId);
    $user_cnt = $kingdomLib->checkUserAvailable($this->userId);
    //$kingdom_cnt = $kingdomLib->checkKingdomAlreadyExisted($this->kingdomName);
    date_default_timezone_set('Asia/Kolkata');
    $requesterDetails= $kingdomLib->getKingdomUserDetailsWithUsersId($this->userId);
    $kuDetails= $kingdomLib->getKingdomUserDetailsWithRequestUsersId($this->acceptUserId, $requesterDetails['kingdom_id']);
    $kUserData= $kingdomLib->getKingdomUserDetailsUsersId($this->acceptUserId);
    $kingdomDetails= $kingdomLib->getKingdomDetails($requesterDetails['kingdom_id']);
    $kingdomUserCntWithoutReq= $kingdomLib->getKingdomUsersCountwithoutRequested($requesterDetails['kingdom_id']);
    if($kingdomUserCntWithoutReq<$kingdomDetails['kingdom_limit']){
      if($kUserData['user_type']==0){
        if(!empty($kuDetails) && $kuDetails['user_type']==0){  
          $kingdomDetails= $kingdomLib->getKingdomDetails($requesterDetails['kingdom_id']);
          $kingdomLib->updateKingdomUser($this->acceptUserId,$requesterDetails['kingdom_id'], array(
            'user_type' => 1
          ));
          
          $kingdomLib->deleteKingdomRequestedMsg($this->acceptUserId, $requesterDetails['kingdom_id'], 7);
          $kingdomLib->deleteKingdomRequestedMsgList($this->acceptUserId, 7);
          $notificationLib->deleteKingdomNotificationOnAccept($this->acceptUserId);

          /*$kingdomLib->updateKingdomMessage($this->acceptUserId,$requesterDetails['kingdom_id'], array(
            'msg_type' => 5
          ));  */
          $sender = $userLib->getUserDetail($this->userId);
          $receiver = $userLib->getUserDetail($this->acceptUserId);
          $sname = empty($sender['name'])?"Guest_".$this->userId:$sender['name'];
          $rname = empty($receiver['name'])?"Guest_".$this->acceptUserId:$receiver['name'];
          /*$kingdomLib->updateKingdomReqMessage($this->msgId, array(
            'kingdom_id' => $requesterDetails['kingdom_id'],
            'msg_type' => 5,
            'chat_type' => 2,
            'is_update' => 1,
            'message' => $rname." accepted by ".$sname,
            'updated_at' => date('Y-m-d H:i:s')
        )); */
          $massageId = $kingdomLib->insertKingdomMsg(array(
            'kingdom_id' => $requesterDetails['kingdom_id'],
            'sent_by' => $this->acceptUserId,
            'received_by' => "",
            'msg_type' => 5,
            'chat_type' => 2,
            'is_update' => 1,
            'message' => $rname." accepted by ".$sname,
            'msg_delete_id' => $this->smsId,
            'created_at' => date('Y-m-d H:i:s')
        ));
        $data = array(
          'user_id'=>$this->acceptUserId,
          'kingdom_id'=>$requesterDetails['kingdom_id']
        );
        $notification = $notificationLib->addNotification(5,CONTENT_TYPE_USER,$this->acceptUserId, $data);
        
          $userLib->updateUser($this->acceptUserId, array(
            'kingdom_id' => $requesterDetails['kingdom_id']
        ));  
          //$kingdomLib->deleteKingdomRequestedUser($this->acceptUserId, $requesterDetails['kingdom_id'], 1);
          $kingdomUsers = $kingdomLib->getKingdomUsersList($requesterDetails['kingdom_id']);
          $kingdomDetailsOnRelics = $kingdomLib->getKingdomUserDetailsOnRelics($requesterDetails['kingdom_id']);
          foreach($kingdomDetailsOnRelics as $ku)
          {
            $userDetails = $userLib->getUserDetail($ku['user_id']);
            $tempUsers = array();
            $tempUsers['rank'] = $ku['srno'];
            $tempUsers['user_id'] = $ku['user_id'];
            $tempUsers['name'] = $userDetails['name'];
            $tempUsers['user_type'] = $ku['user_type'];
            $tempUsers['facebook_id'] = $userDetails['facebook_id'];
            $tempUsers['user_trophies'] = $userDetails['relics'];
            $tempUsers['user_total_gold'] = $userDetails['gold'];
            $tempUsers['donation'] = $ku['donation'];
            $userList[] = $tempUsers;
          }
          $kingdomStatus=1;
          if($requesterDetails['user_type']>=2){
            $kingdomRequestedDetailsOnRelics = $kingdomLib->getKingdomUserRequestedDetailsOnRelics($requesterDetails['kingdom_id']);
            foreach($kingdomRequestedDetailsOnRelics as $kru)
            {
              $userRDetails = $userLib->getUserDetail($ku['user_id']);
              $tempRequestedUsers = array();
              $tempRequestedUsers['rank'] = $kru['srno'];
              $tempRequestedUsers['user_id'] = $kru['user_id'];
              $tempRequestedUsers['name'] = $userRDetails['name'];
              $tempRequestedUsers['user_type'] = $kru['user_type'];
              $tempRequestedUsers['facebook_id'] = $userRDetails['facebook_id'];
              $tempRequestedUsers['user_trophies'] = $userRDetails['relics'];
              $tempRequestedUsers['user_total_gold'] = $userRDetails['gold'];
              $tempRequestedUsers['donation'] = $kru['donation'];
              $requestedUserList[] = $tempRequestedUsers;
            }
          }
        }else{
          $this->setResponse('CUSTOM_ERROR', array('error'=>'Accepting User Id not valid..'));
          return new ArrayObject();
        }
      }else{
        $this->setResponse('USER_ALREADY_IN_KINGDOM');
          return new ArrayObject();
      }
    }else{
      $kingdomStatus=6;
    }
    
    
    $result['kingdom_id'] = $requesterDetails['kingdom_id'];
    $result['kingdom_name'] = $kingdomDetails['kingdom_name'];
    $result['kingdom_type'] = $kingdomDetails['kingdom_type'];
    $result['kingdom_member_limit'] = $kingdomDetails['kingdom_limit'];
    $result['kingdom_shield_id'] = $kingdomDetails['kingdom_shield_id'];
    $result['kingdom_desc'] = $kingdomDetails['kingdom_desc'];
    $result['kingdom_location'] =  $kingdomDetails['kingdom_location'];
    $result['kingdom_req_cup_amt'] = $kingdomDetails['kingdom_req_cup_amt'];
    $result['kingdom_success'] = $kingdomStatus;
    $result['kingdom_user_desc']="User Type Code = 0 : Requested, 1 : Member, 2 : Admin, 3: Co-Leader";
    //$userList[]=$userDetails;
    $result['kingdom_userlist']=$userList;
    if($requesterDetails['user_type']>=2){
      $result['kingdom_requested_userlist']=$requestedUserList;
    }

    $this->setResponse('SUCCESS');
    return $result;
  }
}

