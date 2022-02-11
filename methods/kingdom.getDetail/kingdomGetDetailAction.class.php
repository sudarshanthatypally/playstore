<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for userGetDetail Action
 */
class kingdomGetDetailAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=kingdom.getDetail", tags={"Kingdom"}, 
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
    //$cardLib = autoload::loadLibrary('queryLib', 'card');
    //$roomLib = autoload::loadLibrary('queryLib', 'room');
    //$badgeLib = autoload::loadLibrary('queryLib', 'badge');
    //$deckLib = autoload::loadLibrary('queryLib', 'deck');
    $kingdomLib->deleteKingdomUsersIfNotExists();
    $kingdomLib->deleteKingdomUsersMsgIfNotExists();
    $result = $userList =$requestedUserList =$kickedUserList= $deckList = $temp = array();

    $userDetail = $userLib->getUserDetail($this->userId);
   
    $kingdomUsers = $kingdomLib->getKingdomUsersList($this->kingdomId);
    $kingdomDetails= $kingdomLib->getKingdomDetails($this->kingdomId);
    $kd= $kingdomLib->getKingdomTotalRelics($this->kingdomId);
    $requesterDetails= $kingdomLib->getKingdomUserDetailsWithUsersId($this->userId);
    $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelics($this->kingdomId);
    $uCount=1;
    //$user1to10 = $user11to20 = $user21to30 = $user31to40 = $user41to50 =0;
    foreach($kingdomDetailsOnRelics as $ku){
      $userDetails = $userLib->getUserDetail($ku['user_id']);
      $tempUsers = array();
      $tempUsers['rank'] = $ku['srno'];
      $tempUsers['user_id'] = $ku['user_id'];
      $tempUsers['name'] = $userDetails['name'];
      $tempUsers['user_type']=$ku['user_type'];
      $tempUsers['facebook_id']=$userDetails['facebook_id'];
      $tempUsers['user_trophies']=$userDetails['relics'];
      $tempUsers['donation']=$ku['donation'];
      $userList[] = $tempUsers;
     /* $kingdomRankUser = $kingdomLib->getKingdomRankInventory($tempUsers['rank']);
      if($uCount<=10){
        $user1to10+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      if($uCount>=11 && $uCount<=20){
        $user11to20+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      if($uCount>=21 && $uCount<=30){
        $user21to30+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      if($uCount>=31 && $uCount<=40){
        $user31to40+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }
      if($uCount>=41 && $uCount<=50){
        $user41to50+=$userDetails['relics'] * ($kingdomRankUser['percentage'] / 100);
      }*/
      $uCount++;
    }
    if($requesterDetails['user_type']>=2){
      $kingdomRequestedDetailsOnRelics = $kingdomLib->getKingdomUserRequestedDetailsOnRelics($this->kingdomId);
      $kingdomKickedDetailsOnRelics = $kingdomLib->getKingdomUserKickedDetailsOnRelics($requesterDetails['kingdom_id']);
      foreach($kingdomRequestedDetailsOnRelics as $kru)
      {
        $userRDetails = $userLib->getUserDetail($kru['user_id']);
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
      foreach($kingdomKickedDetailsOnRelics as $kku)
      {
        $userKDetails = $userLib->getUserDetail($kku['user_id']);
        $tempKickedUsers = array();
        $tempKickedUsers['rank'] = $kku['srno'];
        $tempKickedUsers['user_id'] = $kku['user_id'];
        $tempKickedUsers['name'] = $userKDetails['name'];
        $tempKickedUsers['user_type'] = $kku['user_type'];
        $tempKickedUsers['facebook_id'] = $userKDetails['facebook_id'];
        $tempKickedUsers['user_trophies'] = $userKDetails['relics'];
        $tempKickedUsers['user_total_gold'] = $userKDetails['gold'];
        $tempKickedUsers['donation'] = $kku['donation'];
        $tempKickedUsers['kick_msg'] = $kku['kick_msg'];
        $kickedUserList[] = $tempKickedUsers;
      }
    }
    $kingdomRequestedDetailsOnRelics = $kingdomLib->getKingdomUserRequestedDetailsOnRelics($this->kingdomId);
    //$userCntList=count($userList);
    //$totalRelics= $user1to10 + $user11to20 + $user21to30+ $user31to40 +$user41to50;
    $totalRelics= $kingdomLib->getKingdomRankRelicsTotal($this->kingdomId);
    $result['kingdom_id'] = $this->kingdomId;
    $result['kingdom_name'] = $kingdomDetails['kingdom_name'];
    $result['kingdom_type'] = $kingdomDetails['kingdom_type'];
    $result['kingdom_member_limit'] = $kingdomDetails['kingdom_limit'];
    $result['kingdom_shield_id'] = $kingdomDetails['kingdom_shield_id'];
    $result['kingdom_desc'] = $kingdomDetails['kingdom_desc'];
    $result['trophies']=round($totalRelics); //$kd['total']; 
    $result['kingdom_location'] =  $kingdomDetails['kingdom_location'];
    $result['kingdom_req_cup_amt'] = $kingdomDetails['kingdom_req_cup_amt'];
    $result['kingdom_user_desc']="User Type Code = 0 : Requested, 1 : Member, 2 : Admin";
    //$userList[]=$userDetails;
    $result['kingdom_user_count']=count($userList);
    $result['kingdom_userlist']=$userList;
    if($requesterDetails['user_type']>=2){
      $result['kingdom_requested_userlist']=$requestedUserList;
      $result['kingdom_kicked_userlist']=$kickedUserList;
    }
    $this->setResponse('SUCCESS');
    return $result;
  }
}
