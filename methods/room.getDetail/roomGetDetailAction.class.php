<?php
/**
 * Author : Abhijth Shetty
 * Date   : 05-01-2018
 * Desc   : This is a controller file for roomGetDetail Action
 */
class roomGetDetailAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=room.getDetail", tags={"Rooms"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="type", name="type", description="The type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="match_type", name="match_type", description="The type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="waiting_room_id", name="waiting_room_id", description="The waiting_room_id specific to this event",
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
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    $aiLib = autoload::loadLibrary('queryLib', 'ai');
    date_default_timezone_set('Asia/Kolkata');
    $result = $users = array();
    $remainingTime = 0;
    $roomId = -1;
    $waitingRoomPlayer = $roomLib->getWaitingRoomDetail($this->waitingRoomId);


    //Calculating remaining time and setting the room status
    $remainingTime = ((($waitingRoomPlayer['entry_time'] + ROOM_SEARCH_TIMEOUT_TIME) - time()) < 0) ? 0: ($waitingRoomPlayer['entry_time'] + ROOM_SEARCH_TIMEOUT_TIME) - time();

    //Player cancels the finding opponent
    if($this->type == CANCEL_SEARCH)
    {
      $remainingTime = 0;
      $roomLib->updateWaitingRoom($waitingRoomPlayer['waiting_room_id'], array('status' => CONTENT_CLOSED));
    }
    $a=0;
    try{
      if($waitingRoomPlayer['room_id'] > 0)
      {
        $a=1;
        $roomId = $waitingRoomPlayer['room_id'];
        $remainingTime = 0;
        print_log($roomId);
        //Get the roomPlayers .
        $roomPlayers = $roomLib->getPlayersForRoomId($waitingRoomPlayer['room_id']);
        if($this->match_type==2){
          $users = $roomLib->formatMatchingPlayerwithType($roomPlayers);
        }elseif($this->match_type==3){
          $users = $roomLib->formatMatchingPlayerwithType($roomPlayers);
        }else{
          $users = $roomLib->formatMatchingPlayer($roomPlayers);
        }
      }elseif($remainingTime == 0 &&$this->type != CANCEL_SEARCH && !$waitingRoomPlayer['room_id'] > 0 && $this->match_type!=3){
        $a=2;
        //$roomLib->updateWaitingRoom($waitingRoomPlayer['waiting_room_id'], array('status' => CONTENT_CLOSED));
        $roomId = $aiLib->getAiPlayerForUser($waitingRoomPlayer['user_id'], $waitingRoomPlayer['waiting_room_id']);
        $roomPlayers = $roomLib->getPlayersForRoomId($roomId);
        if($this->match_type==2){
          $users = $roomLib->formatMatchingPlayerwithType($roomPlayers);
        }elseif($this->match_type==3){
          $roomId = $roomLib->getOppPlayerForUser($waitingRoomPlayer['user_id'], $waitingRoomPlayer['waiting_room_id']);
          $roomPlayers = $roomLib->getPlayersForRoomId($roomId);
          //$roomPlayersT3 = $roomLib->getPlayersForRoomId($waitingRoomPlayer['room_id']);
          $users = $roomLib->formatMatchingPlayerwithType($roomPlayers);
         /* $msg="Player not found";
          $this->setResponse('SUCCESS');
          return array('msg' => $msg);*/
        }else{
          $users = $roomLib->formatMatchingPlayer($roomPlayers);
        }
      }
      print_log("=============================== user Id ===================================");
      print_log($a);
      print_log($this->match_type);
      print_log("===========================================================================");
    }catch(Exception $e) {
      print_log("=============================== exception ===================================");
      print_log($e->getMessage());
      print_log("========================================================================");
    }
    
    

    $this->setResponse('SUCCESS');
    return array('room_id' => $roomId, 'remaining_time' => $remainingTime, 'users' => $users );
  }
}
