<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for cardGetMasterList Action
 */
class kingdomSearchAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=kingdom.search", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="search_name", name="search_name", description="The search_name specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_type", name="kingdom_type", description="The kingdom_type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kingdom_req_cup_amt", name="kingdom_req_cup_amt", description="The kingdom_req_cup_amt specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="req_warrior", name="req_warrior", description="The req_warrior specific to this event",
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

    $paramList = $result = array();

    // Get the List of all the Master Card
    //$cardList = $cardLib->getMasterCardListWithStadium();  
    //$kingdomList=$kingdomLib->getKingdomListWithRank();
   /* if($this->searchName != "")
    {
      $paramList['kingdom_name'] = $this->searchName;
    }*/
   /* if($this->kingdomType != "")
    {
      $paramList['kingdom_type'] = $this->kingdomType;
    }*/
    /*if($this->requiredCups != "")
    {
      $paramList['kingdom_req_cup_amt'] = $this->requiredCups;
    }
   if($this->reqWarrior != "")
    {
      $paramList['req_warrior'] = $this->reqWarrior;
    }*/
 
    $kingdomList=$kingdomLib->getKingdomListSearch($this->searchName, $this->kingdomType, $this->requiredCups, $this->reqWarrior);
    //print_log($kl);
    //$kingdomList=$kingdomLib->getKingdomListWithRankOnSearch($this->searchName);
    //print_log($kingdomList);
    if(!empty($kingdomList)){
      foreach ($kingdomList as $kingdom)
      {
        $totalRelics=$kingdomLib->getKingdomTotalRelics($kingdom['kingdom_id']);
        $totalUserRelics= $kingdomLib->getKingdomRankRelicsTotal($kingdom['kingdom_id']);
        $kingdomUserInfo = $temp = array();
        $temp['kingdom_id'] = $kingdom['kingdom_id'];
        $temp['kingdom_name'] = $kingdom['kingdom_name'];
        $temp['kingdom_desc'] = $kingdom['kingdom_desc'];
        $temp['kingdom_shield_id'] = $kingdom['kingdom_shield_id'];
        $temp['kingdom_req_cup_amt'] = $kingdom['kingdom_req_cup_amt'];
        $temp['kingdom_location'] = $kingdom['kingdom_location'];
        $temp['kingdom_type'] = $kingdom['kingdom_type'];
        $temp['status'] = $kingdom['status'];
        //$temp['trophies'] = $kingdom['trophies'];
        $temp['trophies']= round($totalUserRelics);
        $temp['total_donation']= $totalRelics['total_donation'];
        $temp['kingdom_rank'] = $kingdom['srno'];
        $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelicsCount($kingdom['kingdom_id']);
        /*$kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelics($kingdom['kingdom_id']);
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
        $temp['kingdom_users_count'] = $kingdomDetailsOnRelics;
        $result[] = $temp;  
        
      }
    }else{
      $this->setResponse('CUSTOM_ERROR', array('error'=>'No Result Found..'));
      return new ArrayObject();
    }
    $this->setResponse('SUCCESS');
    return array('kingdom_list' => $result);
  }
}
