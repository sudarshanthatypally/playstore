<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for cardGetMasterList Action
 */
class kingdomReceiveAction extends baseAction{

/**
   * @OA\Get(path="?methodName=kingdom.receive", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
    * @OA\Parameter(parameter="kingdom_id", name="kingdom_id", description="The kingdom_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="last_msg_id", name="last_msg_id", description="The last_msg_id specific to this event",
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
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $inviteLib = autoload::loadLibrary('queryLib', 'invite');

    $result = array();
    
    // Get the List of all the Master Card
    //$cardList = $cardLib->getMasterCardListWithStadium();
    $kingdomMsgList=$kingdomLib->getKingdomMsgList($this->kingdomId, $this->lastMsgId);
    $msgAvailable=$kingdomLib->getCheckMsgAvailableCount($this->kingdomId);
    //print_log("list::".$kingdomMsgList);
    $msg_cnt=0;
    foreach ($kingdomMsgList as $msg)
    {
      try{
        if($msg['msg_type']==3 && $msg['battle_state']==1){
          $uD = $userLib->getUserDetail($msg['sent_by']);
          if($uD['last_access_time'] < time()-2){
            //$kingdomLib->deleteKingdomRequestedMsgType($msg['sent_by'], $msg['msg_type']);
            //$deletemsgId = $kingdomLib->deleteKingdomRequestedMsgList($msg['sent_by'], $msg['msg_type']);
            $updateMs = $kingdomLib->updateKingdomReqMessage($msg['km_id'], array('battle_state'=>6));
            //$result['msg_delete_id']=$msg['km_id'];
            $msg['battle_state']=6;
          }
        }
      }catch(Exception $e) {
        $this->setResponse('SUCCESS');
        return array('Caught exception: '=>$e->getMessage()."\n");         
      }
      
        
      
     //$totalRelics=$kingdomLib->getKingdomTotalRelics($kingdom['kingdom_id']);
      $kUser = $kingdomLib->getKingdomUserDetail($msg['sent_by']);
      $kingdomUserInfo = $temp = $temp1 = array();
      $userV = $userLib->getUserDetail($msg['sent_by']);
      $temp['last_msg_id'] = $msg['km_id'];
      $temp['kingdom_id'] = $msg['kingdom_id'];
      $temp['sent_by_id'] = $msg['sent_by'];
      $temp['received_by_id'] = $msg['received_by']; 
      $temp['kingdom_msg_type'] = $msg['msg_type'];
      $temp['kingdom_chat_type'] = $msg['chat_type']; 

      if($msg['msg_type']==3 && $msg['sent_by']==$this->userId){
        if($msg['battle_state']==4){
          $bstate = 4;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['sent_by']);
        }elseif($msg['battle_state']==3){
          $bstate = 3;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['sent_by']);
        }elseif($msg['battle_state']==5){
          $bstate = 5;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['received_by']);
        }elseif($msg['battle_state']==6){
          $bstate = 6;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['sent_by']);
        }else{
          $bstate = 1;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['sent_by']);
        }
      }else{
        if($msg['battle_state']==4){
          $bstate = 4;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['sent_by']);
        }elseif($msg['battle_state']==3){
          $bstate = 3;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['sent_by']);
        }elseif($msg['battle_state']==5){
          $bstate = 5;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['received_by']);
        }elseif($msg['battle_state']==6){
          $bstate = 6;  // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['received_by']);
        }
        else{
          $bstate = 2; // 1 for requested , 2 for pending, 3 for result , 4 for cancel
          $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['sent_by']);
        }
      }
      $temp1['battle_type'] = $msg['battle_type'];
      $temp1['battle_state']= $bstate;
      $temp1['requested_userid']= $msg['sent_by'];  
      $temp1['result']= "";
      $temp1['battle_trophies']= "";
      $temp1['battle_token']= $frndlyBattleDetails['invite_token'];
      if($msg['msg_type']==3){
        $temp['kingdomfrindbattle']=$temp1;
      }
      $temp['message'] = $msg['message'];
      $temp['is_update'] = $msg['is_update'] ;
      $temp['msg_delete_id'] = $msg['msg_delete_id'];
      $temp['username']=$userV['name']; 
      $temp['user_type']=$kUser['user_type'];
      $temp['created_at'] = $msg['created_at'];
      if($msg['is_update']==1){
          $kingdomLib->updateKingdomReqMessage($msg['km_id'], array(
            'is_update' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        )); 
      }
      //$kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelicsCount($kingdom['kingdom_id']);
     /* $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelics($kingdom['kingdom_id']);
      foreach($kingdomDetailsOnRelics as $ku){
        $userDetails = $userLib->getUserDetail($ku['user_id']);
        $tempUsers = array();
        $tempUsers['rank'] = $ku['srno'];
        $tempUsers['user_id'] = $ku['user_id'];
        $tempUsers['name'] = $userDetails['name'];
        $tempUsers['user_type']=$ku['user_type'];
        $tempUsers['facebook_id']=$userDetails['facebook_id'];
        $tempUsers['user_trophies']=$userDetails['relics'];
        $kingdomUserInfo[] = $tempUsers;
      }
      $temp['kingdom_userlist'] = $kingdomUserInfo;*/
      //$temp['kingdom_users_count'] = $kingdomDetailsOnRelics;
      $result[] = $temp;  
      $lst_id = $msg['km_id'];
      $uId=$msg['sent_by'];
      $msg_cnt++;
    }
    //print_log($lst_id);
    if(!empty($lst_id)){
      /*$userLib->updateUser($uId, array(
        'notify_seen_count' => $lst_id
      ));*/
      $userLib->updateUser($this->userId, array('notify_seen_count' => $lst_id));
      //print_log("lastId: ".$lst_id);
      //print_log("uId: ".$this->userId);
    }
    $msg_cnt=!empty($msgAvailable)?1:0;
      
    
    $this->setResponse('SUCCESS');
    return array('message_count'=>$msg_cnt,'message_list' => $result);
  }
}
