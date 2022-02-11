<?php

/**
 * Author : Sudarshan Thatypally
 * Date   : 09-11-2020
 * Desc   : This is a controller file for kingdomCreate Action
 */
class kingdomRequestAction extends baseAction
{
	/**
   * @OA\Get(path="?methodName=kingdom.request", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_id", name="kingdom_id", description="The kingdom_id specific to this event",
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

    $result = array();
    $userList = array();
    $userDetails = array();
    $waitngPlayerRoomId = $kingdomId = $roomId = 0;
    //Get the user Detail.
    $user = $userLib->getUserDetail($this->userId);
    $user_cnt = $kingdomLib->checkUserRequestedAvailable($this->userId, $this->kingdomId);
    //$kingdom_cnt = $kingdomLib->checkKingdomAlreadyExisted($this->kingdomName);
    $kingdomDetails = $kingdomLib->getKingdomDetails($this->kingdomId);
    $userReqCnt = $kingdomLib->getKingdomUsersRequestedCount($this->userId);
    $kingdomUserCntWithoutReq= $kingdomLib->getKingdomUsersCountwithoutRequested($this->kingdomId);
    //if($userReqCnt <5){
      
  if($kingdomUserCntWithoutReq<$kingdomDetails['kingdom_limit']){
    if ($user_cnt == 0) {
      print_log("kingdom_cups::".$kingdomDetails['kingdom_req_cup_amt']);
      if ($user['relics']>=$kingdomDetails['kingdom_req_cup_amt'] || $kingdomDetails['kingdom_req_cup_amt']==0) {
        /*if($user['gold'] >= KINGDOM_GOLD_REQUIRED)
        {*/
        if (!empty($this->kingdomId)) {
          $kingdomUserCnt = $kingdomLib->getKingdomUsersCount($this->kingdomId);
          /*if($kingdomUserCnt ==1){
              $user_type =3;
            }else{
              $user_type=1;
            }*/
          $kingdomUserId = $kingdomLib->insertKingdomUser(array(
            'user_id' => $this->userId,
            'user_type' => 0,
            'avatar_url' => $user['avatar_url'],
            'user_trophies' => $user['relics'],
            'kingdom_id' => $this->kingdomId,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
          ));
          /*$userLib->updateUser($this->userId, array(
                'kingdom_id' => $this->kingdomId
            ));*/
          //'gold' => ($user['gold'] - KINGDOM_GOLD_REQUIRED) 
        }
        $msgId = $kingdomLib->insertKingdomMsg(array(
          'kingdom_id' => $this->kingdomId,
          'sent_by' => $this->userId,
          'received_by' => "",
          'msg_type' => 7,
          'chat_type' => 2,
          'message' => "",
          'created_at' => date('Y-m-d H:i:s')
        ));
        $kingdomUsers = $kingdomLib->getKingdomUsersList($this->kingdomId);
        $kingdomDetailsOnRelics = $kingdomLib->getKingdomUserDetailsOnRelics($this->kingdomId);
        foreach ($kingdomDetailsOnRelics as $ku) {
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
        $kingdomStatus = 1;
        /*}else{
          $kingdomStatus=4;
        }*/
      } else {
        $kingdomStatus = 2;
        /*$this->setResponse('INSUFFICIENT_GOLD');
          return new ArrayObject();*/
      }
      print_log("requested");
    } else {
      $this->setResponse('KINGDOM_USER_ALREADY_EXISTED');
      return new ArrayObject();
    }
  }else{
    print_log("limit exceeded");
    $kingdomStatus = 6;
  }
  $kingdomUserCntWithoutReq= $kingdomLib->getKingdomUsersCountwithoutRequested($this->kingdomId);
    $result['kingdom_id'] = $this->kingdomId;
    $result['kingdom_name'] = $kingdomDetails['kingdom_name'];
    $result['kingdom_type'] = $kingdomDetails['kingdom_type'];
    $result['kingdom_member_limit'] = $kingdomDetails['kingdom_limit'];
    $result['kingdom_shield_id'] = $kingdomDetails['kingdom_shield_id'];
    $result['kingdom_desc'] = $kingdomDetails['kingdom_desc'];
    $result['kingdom_location'] =  $kingdomDetails['kingdom_location'];
    $result['kingdom_req_cup_amt'] = $kingdomDetails['kingdom_req_cup_amt'];
    $result['kingdom_success'] = $kingdomStatus;
    $result['kingdom_users_count'] =$kingdomUserCntWithoutReq;
    $result['kingdom_user_desc'] = "User Type Code = 0 : Requested, 1 : Member, 2 : Admin, 3 : Co-Leader";
    //$userList[]=$userDetails;
    $result['kingdom_userlist'] = $userList;

    $this->setResponse('SUCCESS');
    return $result;
  }
}
