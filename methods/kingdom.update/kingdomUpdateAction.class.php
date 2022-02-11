<?php
/**
 * Author : Abhijth Shetty
 * Date   : 28-12-2017
 * Desc   : This is a controller file for userUpdate Action
 */
class kingdomUpdateAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=kingdom.update", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_id", name="kingdom_id", description="The kingdom_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_name", name="kingdom_name", description="The kingdom_name specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_type", name="kingdom_type", description="The kingdom_type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_limit", name="kingdom_limit", description="The kingdom_limit specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_shield_id", name="kingdom_shield_id", description="The kingdom_shield_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_location", name="kingdom_location", description="The kingdom_location specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_desc", name="kingdom_desc", description="The kingdom_desc specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_req_cups", name="kingdom_req_cups", description="The kingdom_req_cups specific to this event",
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
    //$result = new ArrayObject();
    $userList = $result = array();
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');


    $paramList = array();
    $checkKingdomPermit = $kingdomLib->checkKingdomPermission($this->kingdomId, $this->userId);
    $kingdomDetail = $kingdomLib->getKingdomUsersList($this->kingdomId);
    print_log("---------------------------------------------------------------------------------------------------------");
    print_log($kingdomDetail);
    if(!empty($kingdomDetail)){
      foreach($kingdomDetail as $kingdomData){
        print_log($kingdomData['user_id']);
        if($kingdomData['user_id'] == $this->userId){
          print_log($kingdomData['user_id']);
          if($checkKingdomPermit == 1){
            if($this->kingdomName != "")
            {
              $paramList['kingdom_name'] = $this->kingdomName; 
            }
            if($this->kingdomType != "")
            {
              $paramList['kingdom_type'] = $this->kingdomType;
            }
            if($this->kingdomLimit != "")
            {
              $paramList['kingdom_limit'] = $this->kingdomLimit;
            }
            if($this->shieldId != "")
            {
              $paramList['kingdom_shield_id'] = $this->shieldId;
            }
            if($this->kingdomDesc != "")
            {
              $paramList['kingdom_desc'] = $this->kingdomDesc;
            }
            if($this->kingdomLocation != "")
            {
              $paramList['kingdom_location'] = $this->kingdomLocation;
            }
            if($this->kingdomRequiedCups != "")
            {
              $paramList['kingdom_req_cup_amt'] = $this->kingdomRequiedCups;
            }

            if(!empty($paramList)){
              $kingdomLib->updateKingdom($this->kingdomId, $paramList);
            }
          }
          /*else{
            $this->setResponse('CUSTOM_ERROR', array('error' => 'Users is not permit to Update Kingdom.'));
            return new ArrayObject();
          }*/       
        }
        /*else{
          $this->setResponse('CUSTOM_ERROR', array('error' => 'Users is not valid.'));
          return new ArrayObject();
        }*/
      }
    }else{
      $this->setResponse('CUSTOM_ERROR', array('error' => 'Kingdom Id Invalid.'));
      return new ArrayObject();
    }
    $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelics($this->kingdomId);
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
    $kingdomDetails= $kingdomLib->getKingdomDetails($this->kingdomId);
    $result['kingdom_id'] = $this->kingdomId;
    $result['kingdom_name'] = $kingdomDetails['kingdom_name'];
    $result['kingdom_type'] = $kingdomDetails['kingdom_type'];
    $result['kingdom_member_limit'] = $kingdomDetails['kingdom_limit'];
    $result['kingdom_shield_id'] = $kingdomDetails['kingdom_shield_id'];
    $result['kingdom_desc'] = $kingdomDetails['kingdom_desc'];
    $result['kingdom_location'] =  $kingdomDetails['kingdom_location'];
    $result['kingdom_req_cup_amt'] = $kingdomDetails['kingdom_req_cup_amt'];
    $result['kingdom_status']="Kingdom Details updated successfully.";
    $result['kingdom_userlist']=$userList;
    $this->setResponse('SUCCESS');
    return $result;
  }
}