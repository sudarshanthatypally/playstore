<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 17-11-2020
 * Desc   : This is a controller file for kingdomCreate Action
 */
class kingdomLeaveAction extends baseAction
{
	/**
   * @OA\Get(path="?methodName=kingdom.leave", tags={"Kingdom"}, 
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
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');

    $result = array();
    $userList = array();
    $userDetails = array();
    $waitngPlayerRoomId = $kingdomId = $roomId = 0;
    //Get the user Detail.
    $user = $userLib->getUserDetail($this->userId);
    $user_cnt = $kingdomLib->checkUserAvailable($this->userId);
    $kingdomStatus=0;
    $kuDetails= $kingdomLib->getKingdomUserDetailsWithUsersId($this->userId);
    $kingdomId=$kuDetails['kingdom_id'];
    $kingdomDetails= $kingdomLib->getKingdomDetails($kingdomId);
    //$kingdomId=$kuDetails['kingdom_id'];
    //print_log("userCount:".$user_cnt);
    print_log($kingdomId);
    $kingdomLib->deleteKingdomUser($this->userId);
    $userLib->updateUser($this->userId, array('kingdom_id' => 0));
    if($kuDetails['user_type']==2){
      $latestDetails= $kingdomLib->getLatestKingdomUserDetails($kingdomId);
      print_log($latestDetails);
      if(!empty($latestDetails)){
        $kingdomLib->updateKingdomUser($latestDetails['user_id'], $kingdomId,array('user_type' => 2));
        $kingdomStatus=2;
      }else{
        $kingdomLib->deleteKingdom($kingdomId);
        $kingdomStatus=1;
      }
    }
    $msgId = $kingdomLib->insertKingdomMsg(array(
      'kingdom_id' => $kingdomId,
      'sent_by' => $this->userId,
      'received_by' => "",
      'msg_type' => 6,
      'chat_type' => 2,
      'message' => "",
      'created_at' => date('Y-m-d H:i:s')
  ));
    $kingdomDetailsOnRelics = $kingdomLib->getKingdomUserDetailsOnRelics($kingdomId);
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
    $result['kingdom_id'] = $kingdomId;
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

    $this->setResponse('SUCCESS');
    return $result;
  }
}

