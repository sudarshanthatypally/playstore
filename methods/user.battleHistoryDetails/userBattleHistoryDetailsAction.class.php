<?php
/**
 * Author : Abhijth Shetty
 * Date   : 05-01-2018
 * Desc   : This is a controller file for roomGetDetail Action
 */
class userBattleHistoryDetailsAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=user.battleHistoryDetails", tags={"Battles"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
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
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    date_default_timezone_set('Asia/Kolkata');
    $result = $users = array();
    $remainingTime = 0;
    $kingdomLib->deleteBattleRecordsByDate();
    $battleHist = $userLib->getBattleHistoryList($this->userId);
    $temp = array();
    foreach ($battleHist as $bhList) {
        if(!empty($bhList['room_id'])){
          $userTrophies = $roomLib->getMatchPlayersTrophies($bhList['user_winstatus'], $bhList['user_stadium']);
          $opponentTrophies = $roomLib->getMatchPlayersTrophies($bhList['opponent_winstatus'], $bhList['opp_stadium']);
          $temp1=array();
          $temp1['room_id'] = $bhList['room_id'];
          //$temp1['user_id'] = $bhList['user_id'];
          //$temp1['opponent_id'] = $bhList['opponent_id']; 
          $temp1['player_crown_count'] = $bhList['user_circlet'];
          $temp1['opponent_crown_count'] = $bhList['opponent_circlet'];
          $temp1['playerBattleResult'] = $bhList['user_winstatus'];
          $temp1['match_status'] = $bhList['user_winstatus'];
          $temp1['playerTrophies'] = $bhList['user_trophies'];
          $temp1['opponentTrophies'] = $bhList['opponent_trophies'];
          $temp1['opponentBattleResult'] = $bhList['opponent_winstatus'];
          $temp1['PlayerBattletrophies'] = $userTrophies;
          $temp1['opponentBattletrophies'] = $opponentTrophies;
          $temp1['battleTime'] = $bhList['created_at'];
          //$temp1['battle_player_deck']= json_decode($bhList(['userDeckLst']));
          //$temp1['battle_opp_deck']= json_decode($bhList(['oppDeckLst']));
          //$roomPlayers = $roomLib->getPlayersForRoomId($bhList['room_id']);
          $users = $roomLib->matchingPlayerDetails($this->userId, $bhList['opponent_id'],$bhList['userDeckLst'],$bhList['oppDeckLst']);
          $temp1['battle_player']= $users; 
          //$temp1['battle_players']= json_decode($bhList(['userDeckLst']));
          $temp[] = $temp1; 
        } 
    }
    $result = $temp; 
      
    //$roomId = -1;
    // $waitingRoomPlayer = $roomLib->getWaitingRoomDetail($this->roomId);
    //$roomPlayers = $roomLib->getPlayersForRoomId($this->roomId);
    //$users = $roomLib->matchingPlayerDetails($roomPlayers);
    $this->setResponse('SUCCESS');
    return array('listBattleHistory'=>$result);
  }
}
