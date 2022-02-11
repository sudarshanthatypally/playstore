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
   * @OA\Parameter(parameter="battle_type", name="battle_type", description="The battle_type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="battle_state", name="battle_state", description="The battle_state specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="battle_isavailable", name="battle_isavailable", description="The battle_isavailable specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="battle_msg_id", name="battle_msg_id", description="The battle_msg_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_msg", name="kingdom_msg", description="The kingdom_msg specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="request_type", name="request_type", description="The request_type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="master_card_id", name="master_card_id", description="The master_card_id specific to this event",
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
    if($this->kingdomMsgType==1){
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
    }
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
          $inviteLib->updateBattleInvite($frndlyBattleDetails['friendly_invite_id'], array('accepted_user_id'=>$this->userId));
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
    }
    
    
    if($this->kingdomMsgType==2){
      $cardRequestData = $kingdomLib->getRequestedOfCardRequestDetail($this->senderId,$this->kingdomMsgRequestType, date('Y-m-d H:i:s'));
      if($this->kingdomMsgRequestType==1){
        if(!empty($cardRequestData)){
          //$diff=strtotime($cardRequestData['end_time'])-strtotime(date('Y-m-d H:i:s'));
          //$this->setResponse('SUCCESS');
          //return "Card Already requested, Remaing time is $diff";
          $seconds = strtotime($cardRequestData['end_time'])-time();

          $days = floor($seconds / 86400);
          $seconds %= 86400;

          $hours = floor($seconds / 3600);
          $seconds %= 3600;

          $minutes = floor($seconds / 60);
          $seconds %= 60;
          $result['message_toshow'] = "Card Already requested, Remaing time is :";
          if($days!=0){
            $result['message_toshow'] .= "$days days: "; 
          }
          if($hours!=0){
            if($hours<=1){
              $result['message_toshow'] .= "$hours hr: ";
            }else{
              $result['message_toshow'] .= "$hours hrs: ";
            }
            
          }
          if($minutes!=0){
            $result['message_toshow'] .= "$minutes min: ";
          }
          if($seconds!=0){
            $result['message_toshow'] .= "$seconds sec";
          }
          $this->setResponse('SUCCESS');
          return $result;
        }else{
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
        }
        $temp2=array();
        $endtime = date("Y-m-d H:i:s", strtotime('+8 hours'));
        $requestMsgId = $kingdomLib->insertKingdomCardRequest(array(
          'master_card_id' => $this->masterCardId,
          'card_count' => "",
          'request_type' => $this->kingdomMsgRequestType,
          'msg_id' => $msgId,
          'user_id' => $this->senderId,
          'reciever_id' => $this->receiverId,
          'end_time' => $endtime,
          'status' => "",
          'created_by' => $this->senderId, 
          'created_at' => date('Y-m-d H:i:s')
        ));
        $userCardR=$kingdomLib->getUserCardDetail($this->senderId,$this->masterCardId); //reciever
        $temp2['master_card_id'] = $this->masterCardId;
        $temp2['card_count']= empty($cardRequestData['card_count'])?0:$cardRequestData['card_count'];
        $temp2['request_type'] = $this->kingdomMsgRequestType;
        $temp2['msg_id']= $msgId;  
        $temp2['end_time']= $endtime;
        $temp2['total_cards']= $userCardR['user_card_count']; 
        $temp2['total_cards_recieved']= empty($cardRequestData['card_count'])?0:$cardRequestData['card_count'];
        $temp2['max_card_per_user']= 10;
        $temp2['max_card_count']= 40;
      }
      if($this->kingdomMsgRequestType==2){
        $userCardDataS=$kingdomLib->getUserCardDetail($this->senderId,$this->masterCardId); //sender
        $userCardDataR=$kingdomLib->getUserCardDetail($this->receiverId,$this->masterCardId); //reciever
        if(empty($userCardDataS) || $userCardDataS['user_card_count']<=1){
          $this->setResponse('SUCCESS');
          return "You dont have Enough Cards";
        }else{
          $cardRequestData = $kingdomLib->getRequestedOfCardRequestDetail($this->receiverId,1, date('Y-m-d H:i:s'));
          $cardDonaterData = $kingdomLib->getRequestedOfCardRequestDetail($this->senderId, 2, date('Y-m-d H:i:s'));
          if($cardRequestData['card_count']>=40){
            $is_delete=1;
          }else{
            if($cardDonaterData['card_count']<=10){
              //$endtime = date("Y-m-d H:i:s", strtotime('+8 hours'));
              if(empty($cardDonaterData)){
                $deletedId = $cardRequestData['msg_id'];
                $msgId = $kingdomLib->insertKingdomMsg(array(
                  'kingdom_id' => $this->kingdomId,
                  'sent_by' => $this->receiverId,
                  'received_by' => $this->senderId,
                  'msg_type' => $this->kingdomMsgType,
                  'chat_type' => $this->kingdomChatType,
                  'battle_type' => $this->battleType,
                  'battle_state' => $this->battleState,
                  'message' => $this->kingdomMsg,
                  'msg_delete_id' => empty($deletedId)?"":$deletedId,
                  'created_at' => $cardRequestData['created_at']
                ));
                
                $requestMsgId = $kingdomLib->insertKingdomCardRequest(array(
                  'master_card_id' => $this->masterCardId,
                  'card_count' => $cardDonaterData['card_count']+1,
                  'request_type' => $this->kingdomMsgRequestType,
                  'msg_id' => $deletedId,
                  'user_id' => $this->senderId,
                  'reciever_id' => $this->receiverId,
                  'end_time' => $cardRequestData['end_time'],
                  'status' => "",
                  'created_by' => $this->senderId, 
                  'created_at' => date('Y-m-d H:i:s')
                ));
              }else{
                $deletedId = $cardRequestData['msg_id'];
                $msgId = $kingdomLib->insertKingdomMsg(array(
                  'kingdom_id' => $this->kingdomId,
                  'sent_by' => $this->receiverId,
                  'received_by' => $this->senderId,
                  'msg_type' => $this->kingdomMsgType,
                  'chat_type' => $this->kingdomChatType,
                  'battle_type' => $this->battleType,
                  'battle_state' => $this->battleState,
                  'message' => $this->kingdomMsg,
                  'msg_delete_id' => empty($deletedId)?"":$deletedId, 
                  'created_at' => $cardRequestData['created_at']
                ));
                $kingdomLib->updateCardReqMessage($cardDonaterData['card_request_inventory_id'], array(
                  'msg_id' => $deletedId,
                  'card_count' => $cardDonaterData['card_count']+1,
                  'updated_by' =>$this->senderId,
                  'updated_at' => date('Y-m-d H:i:s')
              ));
              }
              $kingdomLib->updateCardReqMessage($cardRequestData['card_request_inventory_id'], array(
                  'msg_id' => $deletedId,
                  'card_count' => $cardRequestData['card_count']+1,
                  'updated_by' =>$this->senderId,
                  'updated_at' => date('Y-m-d H:i:s')
              ));
              $kingdomLib->updateUserCard($this->receiverId, $this->masterCardId, array("user_card_count" => $userCardDataR['user_card_count']+1));
              $kingdomLib->updateUserCard($this->senderId, $this->masterCardId, array("user_card_count" => $userCardDataS['user_card_count']-1));
              $cardRequestData = $kingdomLib->getRequestedOfCardRequestDetail($this->receiverId,1, date('Y-m-d H:i:s'));
              $cardDonaterData = $kingdomLib->getRequestedOfCardRequestDetail($this->senderId, 2, date('Y-m-d H:i:s'),$cardRequestData['msg_id']);
              $userCardDataS=$kingdomLib->getUserCardDetail($this->userId,$cardRequestData['master_card_id']);
              print_log("------------msgid-------------::".$msgId);
              $kingdomLib->updateCardReqMessageLastId($deletedId,array(
                'msg_id' => $deletedId,
              ));
              $kingdomLib->updateKingdomReqMessage($deletedId, array(
                'battle_state' => 4
            ));
              $kingdomLib->deleteKindomMsgByExceptId($msgId,$deletedId);
              //$kingdomLib->deleteKindomMsgById($deletedId);
            
              $temp4=array();
              if($userCardDataS['user_card_count']>1 && $this->userId!=$this->receiverId){
                $temp4['is_donatable'] = 1;
              }else{ 
                $temp4['is_donatable'] = 0; 
              }
              if($cardRequestData['card_count']>=40){
                $is_delete=1;
              }
              $userCardR=$kingdomLib->getUserCardDetail($this->senderId,$this->masterCardId); //reciever
              $temp4['is_delete']=(!empty($is_delete)||isset($is_delete))?$is_delete:0;
              $temp4['master_card_id'] = $this->masterCardId;
              $temp4['card_count']= !empty($cardDonaterData['card_count'])?$cardDonaterData['card_count']:1;
              $temp4['request_type'] = $this->kingdomMsgRequestType;
              $temp4['msg_id']= $deletedId;  
              $temp4['end_time']= $cardRequestData['end_time'];
              $temp4['total_cards']= $userCardR['user_card_count']; 
              $temp4['total_cards_recieved']= $cardRequestData['card_count'];
              $temp4['max_card_per_user']= 10;
              $temp4['max_card_count']= 40;

            }else{
              $this->setResponse('SUCCESS');
              return "You've reached the limit of donating..";
            } 
          }
        }
      }
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
    $result['last_msg_id']=!empty($msgId)?$msgId:$temp4['msg_id'];
    $result['kingdom_id'] = $this->kingdomId;
    $result['sent_by_id'] = $this->senderId;
    $result['received_by_id'] = $this->receiverId;
    $result['msg_type'] = $this->kingdomMsgType;
    $result['chat_type'] = $this->kingdomChatType;
    $result['battle_type'] = $this->battleType;
    $result['message'] = $this->kingdomMsg;
    $result['msg_delete_id']=$deletedId;
    if($this->kingdomMsgType==3){
      $result['kingdomfrindbattle']=$temp1;
    }
    if($this->kingdomMsgType==2){
      if($this->kingdomMsgRequestType==1){
        $result['card_request_data']=$temp2;
      }
      if($this->kingdomMsgRequestType==2){
        $result['card_request_data']=$temp4;
      }
    } 
    $result['is_delete']=(!empty($is_delete)||isset($is_delete))?$is_delete:0;
    $result['username']=!empty($userV['name'])?$userV['name']:"Guest_".$this->userId;
    $result['created_at'] = date('Y-m-d H:i:s');

    $this->setResponse('SUCCESS');
    return $result;
   // return array('Kingdomsendresponce' => $result);
  }
}

