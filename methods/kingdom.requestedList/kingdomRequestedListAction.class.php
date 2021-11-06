<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for userGetDetail Action
 */
class kingdomRequestedListAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=kingdom.requestedList", tags={"Kingdom"}, 
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

    $result = $userList = $deckList = $temp = array();

    $userDetail = $userLib->getUserDetail($this->userId);
   
    $kingdomUsers = $kingdomLib->getKingdomUsersRequestedList($this->kingdomId);
    $kingdomDetails= $kingdomLib->getKingdomDetails($this->kingdomId);
    $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserRequestedDetailsOnRelics($this->kingdomId);
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
    }

    $result['kingdom_id'] = $this->kingdomId;
    $result['kingdom_name'] = $kingdomDetails['kingdom_name'];
    $result['kingdom_type'] = $kingdomDetails['kingdom_type'];
    $result['kingdom_limit'] = $kingdomDetails['kingdom_limit'];
    $result['kingdom_shield_id'] = $kingdomDetails['kingdom_shield_id'];
    $result['kingdom_desc'] = $kingdomDetails['kingdom_desc'];
    $result['kingdom_location'] =  $kingdomDetails['kingdom_location'];
    $result['kingdom_req_cup_amt'] = $kingdomDetails['kingdom_req_cup_amt'];
    $result['kingdom_user_desc']="User Type Code = 0 : Requested, 1 : Member, 2 : Admin";
    //$userList[]=$userDetails;
    $result['kingdom_userlist']=$userList;

    $this->setResponse('SUCCESS');
    return $result;
  }
}
