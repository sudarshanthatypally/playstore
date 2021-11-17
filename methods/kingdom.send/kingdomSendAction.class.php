<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 09-11-2020
 * Desc   : This is a controller file for kingdomCreate Action
 */
class kingdomSendAction extends baseAction
{
	/**
   * @OA\Get(path="?methodName=kingdom.send", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_id", name="kingdom_id", description="The kingdom_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="sender_id", name="sender_id", description="The sender_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="receiver_id", name="receiver_id", description="The receiver_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_msg", name="kingdom_msg", description="The kingdom_msg specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_msg_type", name="kingdom_msg_type", description="The kingdom_msg_type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_chat_type", name="kingdom_chat_type", description="The kingdom_chat_type specific to this event",
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
    //$roomLib = autoload::loadLibrary('queryLib', 'room');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $inviteLib = autoload::loadLibrary('queryLib', 'invite');
    //$notificationLib = autoload::loadLibrary('queryLib', 'notification');
    date_default_timezone_set('Asia/Kolkata');
    $result = array(); 
    $userList = array();
    $userDetails = array();
    $waitngPlayerRoomId = $kingdomId = $roomId = 0;
    //Get the user Detail.
    $user = $userLib->getUserDetail($this->userId);
    //$user_cnt = $kingdomLib->checkUserAvailable($this->userId);
    
//echo date('d-m-Y H:i');
    if($this->kingdomMsgType==3){
      if($this->battleState==4){
        $kingdomBattleData = $kingdomLib->getKingdomBattleByState($this->userId);
        $deletemsgId = $kingdomLib->deleteKingdomRequestedMsgList($this->userId, $this->kingdomMsgType);
        $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($this->userId);
        //print_log("deleted::".$deletemsgId);
        //print_log("deleted id from fetched::".$kingdomBattleData['km_id']);
        $result['msg_delete_id']=$kingdomBattleData['km_id'];
        //$result['battle_state'] = $this->battleState; // 1 for requested , 2 for pending, 3 for result
      }elseif($this->battleState==5){
        /*$invitedUser = $userLib->getUserDetail($this->receiverId);
        if($invitedUser['last_access_time'] < time()-60){
          $this->setResponse('PLAYER_OFFLINE');
          return null;
        }*/
        $kmMsg = $kingdomLib->getKingdomBattleByStateMsgType($this->receiverId,$this->kingdomMsgType,1);
        $kingdomBattleData = $kingdomLib->getKingdomBattleByState($this->receiverId);
        if($kmMsg['battle_isavailable']!=1){
            $deletemsgId = $kingdomLib->deleteKingdomRequestedMsgList($this->receiverId, $this->kingdomMsgType);
            //print_log("deleted::".$deletemsgId);
            //print_log("deleted id from fetched::".$kingdomBattleData['km_id']);
           
            //$result['battle_state'] = $this->battleState; // 1 for requested , 2 for pending, 3 for result
          }
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($this->receiverId);
          $result['msg_delete_id']=$kingdomBattleData['km_id'];
      }elseif($this->battleState==10){
        $kingdomMsId = $kingdomLib->getKingdomBattleByState($this->userId);
        if($this->battleIsAvailable==1){
          $kingdomLib->updateKingdomReqMessage($kingdomMsId['km_id'], array(
            'battle_isavailable' => 1,
            'updated_at' => date('Y-m-d H:i:s')
          )); 
        }
        if($this->battleIsAvailable==0){
          $kingdomLib->updateKingdomReqMessage($kingdomMsId['km_id'], array(
            'battle_isavailable' => 0,
            'updated_at' => date('Y-m-d H:i:s')
          )); 
        }
        $result['battle_isavailable'] = $this->battleIsAvailable; 
        $this->setResponse('SUCCESS');
        return $result;
      }
      else{
        $result['battle_state'] = 1; // 1 for requested , 2 for pending, 3 for result
        $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($this->userId);
      }
    }
    
    $kmMsg = $kingdomLib->getKingdomBattleByStateMsgType($this->receiverId,$this->kingdomMsgType,1);
    if($kmMsg['battle_isavailable']!=1){
      $msgId = $kingdomLib->insertKingdomMsg(array(
        'kingdom_id' => $this->kingdomId,
        'sent_by' => $this->senderId,
        'received_by' => $this->receiverId,
        'msg_type' => $this->kingdomMsgType,
        'chat_type' => $this->kingdomChatType,
        'battle_type' => $this->battleType,
        'battle_state' => empty($this->battleState)?"1":$this->battleState,
        'message' => $this->kingdomMsg,
        'msg_delete_id' => empty($kingdomBattleData['km_id'])?"":$kingdomBattleData['km_id'], 
        'created_at' => date('Y-m-d H:i:s')
    ));
    }
    
    /*if(!empty($msgId)){

    }*/
    $temp1=array();
    if($this->kingdomMsgType==3 && $this->senderId==$this->userId){
      if($this->battleState>=3){
        $bstate = $this->battleState;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel, 5 for accept
      }else{
        $bstate = 1;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel, 5 for accept
      }
    }else{
      $bstate = 2; // 1 for requested , 2 for pending, 3 for result , 4 for cancel, 5 for accept
    }

    if($this->kingdomMsgType==3 && $bstate==1){
      
      //add 5 link per hour limitation
      $userInvites = $inviteLib->getFriendlyInviteListWithLimit($this->userId, MAX_INVITE_PER_HOUR);
      /*if(sizeof($userInvites)==MAX_INVITE_PER_HOUR && strtotime($userInvites[MAX_INVITE_PER_HOUR-1]['created_at']) > time()-3600){
        $result['next_invite'] = (strtotime($userInvites[MAX_INVITE_PER_HOUR-1]['created_at'])+3600)-time();
        $this->setResponse('MAX_INVITE_LIMIT_REACHED');
        return $result;
      }*/
      $accessToken = (isset($user['access_token']) ? $user['access_token'] : false);
      $inviteToken = md5(md5($this->userId).md5($accessToken).md5(time()));
      print_log($inviteToken);
     // $inviteToken = md5(md5($this->userId).md5($user['access_token']).md5(time()));

      $inviteLib->insertFriendlyInvite(array('user_id'=>$this->userId, 
        'invite_token'=>$inviteToken,
        'status'=>CONTENT_ACTIVE,
        'created_at'=>date('Y-m-d H:i:s')));
    }
    if(!empty($user['name'])){
      $userName=$user['name'];
    }else{
      $userName="Guest ".$this->userId; 
    }
    $kmMsg = $kingdomLib->getKingdomBattleByStateMsgType($this->receiverId,$this->kingdomMsgType,1);
    $temp1['battle_type'] = $this->battleType;
    $temp1['battle_state']= $bstate;
    $temp1['battle_isavailable'] = !empty($kmMsg['battle_isavailable'])?$kmMsg['battle_isavailable']:0;
    $temp1['requested_userid']= $this->senderId;  
    $temp1['result']= "";
    $temp1['battle_trophies']= "";
    $temp1['referrer_name'] = $userName;  
    $temp1['battle_token']= !empty($inviteToken)?$inviteToken:$frndlyBattleDetails['invite_token'];
    



    $userV = $userLib->getUserDetail($this->userId);
    $result['last_msg_id']=!empty($msgId)?$msgId:"";
    $result['kingdom_id'] = $this->kingdomId;
    $result['sent_by_id'] = $this->senderId;
    $result['received_by_id'] = $this->receiverId;
    $result['msg_type'] = $this->kingdomMsgType;
    $result['chat_type'] = $this->kingdomChatType;
    $result['battle_type'] = $this->battleType;
    $result['message'] = $this->kingdomMsg;
    if($this->kingdomMsgType==3){
      $result['kingdomfrindbattle']=$temp1;
    }
    $result['username']=$userV['name'];
    $result['created_at'] = date('Y-m-d H:i:s');

    $this->setResponse('SUCCESS');
    return $result;
   // return array('Kingdomsendresponce' => $result);
  }
}

