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
    $roomLib = autoload::loadLibrary('queryLib', 'room');

    $result = array();
    date_default_timezone_set('Asia/Kolkata');
    //$userLib->deleteKingdomLoginAccess();
    //$userLib->updateToCancelKingdomChatWithLoginAccess();
    $cancelList = $userLib->getListToCancelKingdomChatWithLoginAccess();
    foreach($cancelList as $cl){
        $deletemsgId = $kingdomLib->deleteKingdomRequestedMsgList($cl['sent_by'], $cl['msg_type']);
        $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($this->userId);
       // $result['msg_delete_id']=$cl['km_id'];

        $msgId = $kingdomLib->insertKingdomMsg(array(
          'kingdom_id' => $cl['kingdom_id'],
          'sent_by' => $cl['sent_by'],
          'received_by' => $cl['received_by'],
          'msg_type' => $cl['msg_type'],
          'chat_type' => $cl['chat_type'],
          'battle_type' => $cl['battle_type'],
          'battle_state' => 4,
          'message' => $cl['message'],
          'msg_delete_id' => $cl['km_id'], 
          'created_at' => date('Y-m-d H:i:s')
      ));
      //$result['last_msg_id']=$msgId;
    }
    $isExisted = $userLib->getKingdomLoginAccess($this->userId);
    
    if(empty($isExisted)){
      $userLib->insertKingdomLoginAccess($this->userId, array(
        'user_id' => $this->userId,
        'last_access' => time(),
        'created_date' => date('Y-m-d H:i:s')));
    }else{
      $userLib->updateKingdomLoginAccess($this->userId, array('last_access' => time()));
    }
    
    // Get the List of all the Master Card
    //$cardList = $cardLib->getMasterCardListWithStadium();
    $kingdomMsgList=$kingdomLib->getKingdomMsgList($this->kingdomId, $this->lastMsgId);
    $msgAvailable=$kingdomLib->getCheckMsgAvailableCount($this->kingdomId); 
    //print_log("list::".$kingdomMsgList);
    $msg_cnt=0;
    foreach ($kingdomMsgList as $msg)
    {
      
      $endtime = date('Y-m-d H:i:s',strtotime('+8 hours',strtotime($msg['created_at'])));
      if((date('Y-m-d H:i:s')<$endtime && $msg['msg_type']==2) || $msg['msg_type']!=2){
        /*try{
        if($msg['msg_type']==3 && $msg['battle_state']==1){
          $uD = $userLib->getUserDetail($msg['sent_by']);
          if($uD['last_access_time'] < time()){
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
      }*/
      
        
      
      //$totalRelics=$kingdomLib->getKingdomTotalRelics($kingdom['kingdom_id']);
        $kUser = $kingdomLib->getKingdomUserDetail($msg['sent_by']);
        $kingdomUserInfo = $temp = $temp1 = array();
        $userV = $userLib->getUserDetail($msg['sent_by']);
        $temp['last_msg_id'] = !empty($msgId)?$msgId:$msg['km_id'];
        $temp['kingdom_id'] = $msg['kingdom_id'];
        $temp['sent_by_id'] = $msg['sent_by'];
        $temp['received_by_id'] = $msg['received_by']; 
        $temp['kingdom_msg_type'] = $msg['msg_type'];
        $temp['kingdom_chat_type'] = $msg['chat_type']; 
        //&& $msg['battle_state']
        if($msg['msg_type']==3){
          $frndbattleHist = $userLib->getFriendlyBattleHistoryList($this->userId, $msg['room_id']);
        if(empty($frndbattleHist)){
            $frndbattleHist = $userLib->getFriendlyBattleHistoryList($msg['sent_by'], $msg['room_id']);
            $tempB = array();
            foreach ($frndbattleHist as $bhList) {
                if(!empty($bhList['room_id'])){
                  $userTrophies = $roomLib->getMatchPlayersTrophies($bhList['user_winstatus'], $bhList['user_stadium']);
                  $opponentTrophies = $roomLib->getMatchPlayersTrophies($bhList['opponent_winstatus'], $bhList['opp_stadium']);
                  $temp2=array();
                  $temp2['room_id'] = $bhList['room_id'];
                  //$temp1['user_id'] = $bhList['user_id'];
                  //$temp1['opponent_id'] = $bhList['opponent_id']; 
                  $temp2['player_crown_count'] = $bhList['opponent_circlet'];
                  $temp2['opponent_crown_count'] = $bhList['user_circlet'];
                  $temp2['playerBattleResult'] = $bhList['opponent_winstatus'];
                  $temp2['match_status'] = $bhList['opponent_winstatus'];
                  $temp2['playerTrophies'] = $bhList['opponent_trophies'];
                  $temp2['opponentTrophies'] = $bhList['user_trophies'];
                  $temp2['opponentBattleResult'] = $bhList['user_winstatus'];
                  $temp2['PlayerBattletrophies'] = $opponentTrophies;
                  $temp2['opponentBattletrophies'] = $userTrophies;
                  $temp2['battleTime'] = $bhList['created_at'];
                  //$temp1['battle_player_deck']= json_decode($bhList(['userDeckLst']));
                  //$temp1['battle_opp_deck']= json_decode($bhList(['oppDeckLst']));
                  //$roomPlayers = $roomLib->getPlayersForRoomId($bhList['room_id']);
                  $users = $roomLib->matchingPlayerDetails($bhList['opponent_id'],$msg['sent_by'],$bhList['oppDeckLst'],$bhList['userDeckLst']);
                  $temp2['battle_player']= $users; 
                  //$temp1['battle_players']= json_decode($bhList(['userDeckLst']));
                  $tempB = $temp2; 
                }  
              }
          }else{
            $tempB = array();
            foreach ($frndbattleHist as $bhList) {
                if(!empty($bhList['room_id'])){
                  $userTrophies = $roomLib->getMatchPlayersTrophies($bhList['user_winstatus'], $bhList['user_stadium']);
                  $opponentTrophies = $roomLib->getMatchPlayersTrophies($bhList['opponent_winstatus'], $bhList['opp_stadium']);
                  $temp2=array();
                  $temp2['room_id'] = $bhList['room_id'];
                  //$temp1['user_id'] = $bhList['user_id'];
                  //$temp1['opponent_id'] = $bhList['opponent_id']; 
                  $temp2['player_crown_count'] = $bhList['user_circlet'];
                  $temp2['opponent_crown_count'] = $bhList['opponent_circlet'];
                  $temp2['playerBattleResult'] = $bhList['user_winstatus'];
                  $temp2['match_status'] = $bhList['user_winstatus'];
                  $temp2['playerTrophies'] = $bhList['user_trophies'];
                  $temp2['opponentTrophies'] = $bhList['opponent_trophies'];
                  $temp2['opponentBattleResult'] = $bhList['opponent_winstatus'];
                  $temp2['PlayerBattletrophies'] = $userTrophies;
                  $temp2['opponentBattletrophies'] = $opponentTrophies;
                  $temp2['battleTime'] = $bhList['created_at'];
                  //$temp1['battle_player_deck']= json_decode($bhList(['userDeckLst']));
                  //$temp1['battle_opp_deck']= json_decode($bhList(['oppDeckLst']));
                  //$roomPlayers = $roomLib->getPlayersForRoomId($bhList['room_id']);
                  $users = $roomLib->matchingPlayerDetails($this->userId, $bhList['opponent_id'],$bhList['userDeckLst'],$bhList['oppDeckLst']);
                  $temp2['battle_player']= $users; 
                  //$temp1['battle_players']= json_decode($bhList(['userDeckLst']));
                  $tempB = $temp2; 
                } 
            }
          }
          $friendlyBattleResult = $tempB; 
        }

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
          }else{
            $bstate = 2; // 1 for requested , 2 for pending, 3 for result , 4 for cancel
            $frndlyBattleDetails = $inviteLib->getFriendlyInviteDetailByUserId($msg['sent_by']);
          }
        }
        $temp1['battle_type'] = $msg['battle_type'];
        if($bstate==5){
          if(($frndlyBattleDetails['user_id']==$msg['sent_by'] && $frndlyBattleDetails['accepted_user_id']==$msg['received_by']) || ($frndlyBattleDetails['user_id']==$msg['received_by'] && $frndlyBattleDetails['accepted_user_id']==$msg['sent_by']) && ($frndlyBattleDetails['accepted_user_id']==$this->userId || $frndlyBattleDetails['user_id']==$this->userId)){
            $bst=5;
          }else{
            $bst=4;
          }
        }else{
          $bst=$bstate;
        }
        $temp1['battle_state']= $bst;
        $temp1['requested_userid']= $msg['sent_by'];  
        $temp1['battle_isavailable']= $msg['battle_isavailable'];  
        $temp1['result']= "";
        $temp1['battle_trophies']= "";
        $temp1['battle_token']= $frndlyBattleDetails['invite_token'];
        if($msg['msg_type']==3){
          if($msg['battle_state']==3){
            $temp1['kingdomFriendly_BattleHistory']=$friendlyBattleResult;
          }
          $temp['kingdomfrindbattle']=$temp1;
        }
        if($msg['msg_type']==2){
          $temp3=array();
          //$cardRequestData = $kingdomLib->getCardRequestDetail($msg['sent_by']);
          $cardDonaterData = $kingdomLib->getRequestedOfCardRequestDetail($this->userId,2, date('Y-m-d H:i:s'), $msg['msg_delete_id']);
          $cardRequestData = $kingdomLib->getRequestedOfCardRequestDetail($msg['sent_by'], 1, date('Y-m-d H:i:s'), $msg['msg_delete_id']);
          $userCardDataS=$kingdomLib->getUserCardDetail($this->userId,$cardRequestData['master_card_id']);
          if($userCardDataS['user_card_count']>1 && $this->userId!=$msg['sent_by']){
            $temp3['is_donatable'] = 1;
          }else{
            $temp3['is_donatable'] = 0;
          }
          if($cardRequestData['card_count']>=40){
            $is_delete=1;
            $kingdomLib->updateKingdomReqMessage($msg['km_id'], array(
              'battle_state' => 4
          ));
          }
          $userCardR=$kingdomLib->getUserCardDetail($this->userId,$cardRequestData['master_card_id']); //reciever
          $temp3['is_delete']=(!empty($is_delete)||isset($is_delete))?$is_delete:0;
          $temp3['master_card_id'] = $cardRequestData['master_card_id'];
          $temp3['card_count']= !empty($cardDonaterData['card_count'])?$cardDonaterData['card_count']:0;
          $temp3['request_type'] = $cardRequestData['request_type'];
          $temp3['msg_id']= $cardRequestData['msg_id'];  
          $temp3['end_time']= $cardRequestData['end_time'];
          $temp3['total_cards']= $userCardR['user_card_count']; 
          $temp3['total_cards_recieved']= $cardRequestData['card_count'];
          $temp3['max_card_per_user']= 10;
          $temp3['max_card_count']= 40;
          $temp['request_card_details']=$temp3;
        
        }
        
        $temp['message'] = $msg['message'];
        $temp['is_update'] = $msg['is_update'] ;
        $temp['msg_delete_id'] = $msg['msg_delete_id'];
        $temp['username']=!empty($userV['name'])?$userV['name']:"Guest_".$msg['sent_by']; 
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
        if($msg['battle_state']!=5 && $msg['battle_state']!=4){
          $msg_cnt++;
        }
      }
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
    $msg_cnt=!empty($msgAvailable)?$msgAvailable:0;
      
    
    $this->setResponse('SUCCESS');
    return array('message_count'=>$msg_cnt,'message_list' => $result);
  }
}
