<?php
/**
 * Author : Abhijth Shetty
 * Date   : 05-01-2018
 * Desc   : This is a controller file for roomCreate Action
 */
class roomCreateAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=room.create", tags={"Rooms"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="room_type", name="room_type", description="The room_type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="invite_token", name="invite_token", description="The invite_token specific to this event",
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
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    $inviteLib = autoload::loadLibrary('queryLib', 'invite');
    $notificationLib = autoload::loadLibrary('queryLib', 'notification');
    $result = array();
    $waitngPlayerRoomId = $roomId = 0;
    date_default_timezone_set('Asia/Kolkata');
    //Get the user Detail.
    $user = $userLib->getUserDetail($this->userId);
    $inviteDetail = $inviteLib->getActiveInviteDetailForInviteToken($this->inviteToken);
    $inviteBattleDetail = $inviteLib->getActiveBattleInviteDetailForInviteToken($this->inviteToken);
    $currentNotification = $notificationLib->getNotificationDetail($inviteDetail['notification_id']);
    $currentBattleNotification = $notificationLib->getNotificationDetail($inviteBattleDetail['notification_id']);

    if($this->roomType==ROOM_TYPE_INVITE) {
      if(empty($this->inviteToken)){
        $this->setResponse('INVITE_TOKEN_MANDATORY');
        return false;
      }
      if(empty($inviteDetail) || ((strtotime($inviteDetail['created_at'])+INVITE_LINK_TIME_OUT_TIME) < time())){
        if(! empty($currentNotification)){
          $data = json_decode($currentNotification['data'],true);
          $data['is_room_active'] = CONTENT_INACTIVE;
          $notificationLib->updateNotification($currentNotification['notification_id'], array('data'=>json_encode($data)));
        }
        $this->setResponse('INVALID_INVITE_TOKEN');
        return false;
      }
      $invitedUser = $userLib->getUserDetail($inviteDetail['user_id']);
      if($invitedUser['last_access_time'] < time()-60){
        $this->setResponse('PLAYER_OFFLINE');
        return null;
      }
    }
    if($this->roomType==ROOM_TYPE_BATTLE) {
      $invitedUser = $userLib->getUserDetail($inviteBattleDetail['user_id']);
      if(empty($this->inviteToken)){
        $this->setResponse('INVITE_TOKEN_MANDATORY');
        return false;
      }
      if(empty($inviteBattleDetail)){
        /*if(!empty($currentBattleNotification)){
          $data = json_decode($currentBattleNotification['data'],true);
          $data['is_room_active'] = CONTENT_INACTIVE;
          //$notificationLib->updateNotification($currentBattleNotification['notification_id'], array('data'=>json_encode($data)));
        }*/
        $this->setResponse('INVALID_INVITE_TOKEN');
        return false;
      }
      //$invitedUser = $userLib->getUserDetail($inviteBattleDetail['user_id']);
     /* if($invitedUser['last_access_time'] < time()-3600){
        $this->setResponse('PLAYER_OFFLINE');
        return null;
      }*/
    }
    //Check requested player still searching for opponent.
    $waitingPlayerDetail = $roomLib->getWaitingPlayerBasedOnActiveStatus($this->userId);

    if($this->roomType!=ROOM_TYPE_BATTLE && $this->roomType!=ROOM_TYPE_INVITE) {
      //If player searching time out then update the status og waiting_room  for that user.
      if($waitingPlayerDetail['entry_time'] && $waitingPlayerDetail['entry_time'] < time() - ROOM_SEARCH_TIMEOUT_TIME ){
        $roomLib->updateWaitingRoom($waitingPlayerDetail['waiting_room_id'], array('status' => CONTENT_CLOSED));
      }
    }
    

    if($this->roomType==ROOM_TYPE_INVITE){

      $waitingRoomId = $roomLib->insertWaitingRoomPlayer(array(
        'user_id' => $this->userId,
        'win_status' => BATTLE_DEFAULT_STATUS,
        'entry_time' => time(),
        'created_at' => date('Y-m-d H:i:s'),
        'status' => CONTENT_PENDING
      ));
      //code for user accepting the invite
      if($inviteDetail['user_id'] != $this->userId && $inviteDetail['status']==CONTENT_ACTIVE){
        $roomId = $roomLib->insertRoom(array(
          'user_id' => $this->userId,
          'created_at' => date('Y-m-d H:i:s'),
          'status' => CONTENT_ACTIVE));
        $data = array(
          'user_id'=>$inviteDetail['user_id'],
          'user_name'=>$user['name'],
          'invite_token'=>$this->inviteToken,
          'is_room_active'=>CONTENT_ACTIVE,
          'accepted_user_id'=>$this->userId,
          'room_id'=>$roomId
        );

        $notification = $notificationLib->addNotification(NOTIFICATION_TYPE_INVITE_ACCEPTED,CONTENT_TYPE_USER,$inviteDetail['user_id'], $data);
        $inviteLib->updateInvite($inviteDetail['invite_id'], array('accepted_user_id'=>$this->userId,'room_id'=>$roomId,'status'=>CONTENT_INPROGRESS, 'notification_id'=>$notification));
      } else{
        $roomId = $inviteDetail['room_id'];
        $inviteLib->updateInvite($inviteDetail['invite_id'], array('status'=>CONTENT_ACCEPTED));
        //update notification data
        $data = json_decode($currentNotification['data'],true);
        $data['is_room_active'] = CONTENT_INACTIVE;
        $notificationLib->updateNotification($currentNotification['notification_id'], array('data'=>json_encode($data)));
      }
      $roomLib->updateWaitingRoom($waitingRoomId, array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));

    }else if($this->roomType==ROOM_TYPE_BATTLE){
      print_log("userId::".$this->userId);
      $waitingRoomId = $roomLib->insertWaitingRoomPlayer(array(
        'user_id' => $this->userId,
        'win_status' => BATTLE_DEFAULT_STATUS,
        'entry_time' => time(),
        'created_at' => date('Y-m-d H:i:s'),
        'status' => CONTENT_PENDING
      ));
      print_log("battleUserId::".$inviteBattleDetail['user_id']);
      //code for user accepting the invite
      if($inviteBattleDetail['user_id'] != $this->userId){
         $roomId = $roomLib->insertRoom(array(
          'user_id' => $this->userId,
          'created_at' => date('Y-m-d H:i:s'),
          'status' => CONTENT_ACTIVE));
          /*$roomData= $roomLib->getRoomDetailForFriendlyBattle($inviteBattleDetail['user_id']);
          $roomId= $roomData['room_id'];*/
          /*$roomData= $roomLib->getRoomDetailForFriendlyBattle($inviteBattleDetail['user_id']);
          $roomId= $roomData['room_id'];*/
          $roomLib->updateWaitingRoom($waitingRoomId, array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));
        /*$data = array(
          'user_id'=>$inviteBattleDetail['user_id'],
          'user_name'=>$user['name'],
          'invite_token'=>$this->inviteToken,
          'is_room_active'=>CONTENT_ACTIVE,
          'accepted_user_id'=>$this->userId,
          'room_id'=>$roomId
        );*/ 

        //$notification = $notificationLib->addNotification(NOTIFICATION_TYPE_INVITE_ACCEPTED,CONTENT_TYPE_USER,$inviteBattleDetail['user_id'], $data);
        //, 'notification_id'=>$notification
       // $inviteBattleDetail = $inviteLib->getActiveBattleInviteDetailForInviteToken($this->inviteToken);
        $inviteLib->updateBattleInvite($inviteBattleDetail['friendly_invite_id'], array('room_id'=>$roomId,'status'=>CONTENT_INPROGRESS));//'accepted_user_id'=>$this->userId,
        //$roomLib->updateWaitingRoom($waitingRoomId, array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));
        //$inviteBattleDetail = $inviteLib->getActiveBattleInviteDetailForInviteToken($this->inviteToken);
        $getWaitingDetails = $roomLib->getWaitingPlayerBasedOnUserId($inviteBattleDetail['user_id']);
        $roomLib->updateWaitingRoom($getWaitingDetails['waiting_room_id'], array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));
        
      } else{
        $roomId = $inviteBattleDetail['room_id'];
        $inviteLib->updateBattleInvite($inviteBattleDetail['friendly_invite_id'], array('status'=>5));
        //update notification data
        /*$data = json_decode($currentBattleNotification['data'],true);
        $data['is_room_active'] = CONTENT_INACTIVE;*/
       // $notificationLib->updateNotification($currentBattleNotification['notification_id'], array('data'=>json_encode($data)));
      }
     $roomLib->updateWaitingRoom($waitingRoomId, array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));  
     $roomLib->deleteAllWaitingRoomUserId($this->userId,$inviteBattleDetail['user_id'],$roomId);
     //$roomLib->deleteAllWaitingRoomUser($inviteBattleDetail['user_id'],$getWaitingDetails['waiting_room_id']);
     //$roomLib->deleteAllWaitingRoomUser($this->userId,$waitingRoomId);
    }else if(empty($waitingPlayerDetail) || ($waitingPlayerDetail['entry_time'] && $waitingPlayerDetail['entry_time'] < time() - ROOM_SEARCH_TIMEOUT_TIME)){
      $waitingRoomId = $roomLib->insertWaitingRoomPlayer(array(
        'user_id' => $this->userId,
        'win_status' => BATTLE_DEFAULT_STATUS,
        'entry_time' => time(),
        'created_at' => date('Y-m-d H:i:s'),
        'status' => CONTENT_PENDING
      ));

      //Find the matching opponent based on same level and closest relics(trophy) count.
      // $matchingPlayer = $roomLib->getMatchingPlayer($waitingRoomId, $this->userId, $user['level_id'], $user['relics'], $user['master_stadium_id']);

      //without level
      //$matchingPlayer = $roomLib->getMatchingPlayer($waitingRoomId, $this->userId, $user['relics'], $user['master_stadium_id']);
      if($this->roomType==ROOM_TYPE_INVITE){
        $matchingPlayer = $roomLib->getMatchingPlayer($waitingRoomId, $this->userId, $user['level_id'], $user['relics'], 1);
      }else{
        $matchingPlayer = $roomLib->getMatchingPlayer($waitingRoomId, $this->userId, $user['level_id'], $user['relics'], $user['master_stadium_id']);
      }
      if($this->roomType==ROOM_TYPE_BATTLE){
        $matchingPlayer = $roomLib->getMatchingPlayer($waitingRoomId, $this->userId, $user['level_id'], $user['relics'], 1);
      }else{
        $matchingPlayer = $roomLib->getMatchingPlayer($waitingRoomId, $this->userId, $user['level_id'], $user['relics'], $user['master_stadium_id']);
      }
      
      //if matching player found then create a room and give room_id to the Matchingplayers.
      if(!empty($matchingPlayer))
      {
        $roomId = $roomLib->insertRoom(array(
          'user_id' => $this->userId,
          'created_at' => date('Y-m-d H:i:s'),
          'status' => CONTENT_ACTIVE));

        //Assign the room to battling player.
        $roomLib->updateWaitingRoom($matchingPlayer['waiting_room_id'], array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));
        $roomLib->updateWaitingRoom($waitingRoomId, array('status' => CONTENT_ACTIVE, 'room_id' => $roomId));
      }
      
    } else {
      $waitingRoomId = $waitingPlayerDetail['waiting_room_id'];
    }

    $result['waiting_room_id'] = $waitingRoomId;

    $this->setResponse('SUCCESS');
    return $result;
  }
}
