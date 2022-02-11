<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 28-10-2020
 * Desc   : This is a controller file for websiteUserLeadershipBoard Action
 */
class websiteUserLeadershipBoardAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=user.leadershipBoard", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="fetchLimit", name="fetchLimit", description="The fetchLimit specific to this event",
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

    $result = $deckList = $temp = array();

    //$userDetail = $userLib->getUserDetail($this->userId);
    
    $limit=empty($this->fetchLimit)?100:$this->fetchLimit;
    //$userRelicsDetails = $userLib->getUserDetailsOnRelics($this->userId, $limit);
    $userRelicsDetail = $userLib->getUserDetailsOnRelics();
    //$result['name'] = $userDetail['name'];
    //$result['total_relic'] = $limit;
    $cnt=0;
    $matched=0;
    foreach ($userRelicsDetail as $userRelics)
    {  
       /* if($this->userId == $userRelics['user_id']){
          $matched=1;
        }*/
        $temp = array();
        $temp['rank'] = $userRelics['srno'];
        $temp['name'] = $userRelics['name'];
        $temp['user_id'] = $userRelics['user_id'];
        $temp['facebook_id']=$userRelics['facebook_id'];
        $temp['relics'] = $userRelics['relics'];
        $temp['avatar_url']=$userRelics['avatar_url'];

        $userBadgeDetail = $userLib->getBadgeByUserRelics($userRelics['relics']);
        $temp['master_badge_id']=$userBadgeDetail['master_badge_id'];
        $temp['title']=$userBadgeDetail['title'];
        $temp['min_relic_count']=$userBadgeDetail['min_relic_count'];
        $temp['max_relic_count']=$userBadgeDetail['max_relic_count'];

        //$temp['abc']=$matched;
        $result[] = $temp;
        //print_log($temp);
        $cnt++;
        if($cnt >= $limit){
          break;
        }
    }
    /*if($matched == 0){
      $userRelicsDetails = $userLib->getUserDetailsOnRelics();
      foreach ($userRelicsDetails as $userRelic)
      {  
        if($userRelic['user_id']==$this->userId){
          $tempi = array();
          $tempi['rank'] = $userRelic['srno'];
          $tempi['name'] = $userRelic['name'];
          $tempi['user_id'] = $userRelic['user_id'];
          $tempi['facebook_id']=$userRelic['facebook_id'];
          $tempi['relics'] = $userRelic['relics'];
          $tempi['avatar_url']=$userRelic['avatar_url'];
         $res[] = $tempi;
         // print_log($res);
          
        }
      }
      //print_log(array_merge($result,$res));
      
      $result=array_merge($res,$result);
    }*/
    //print_log("-------------------------------------------------------------------------------------------------------------------------------------------------------------------------".$this->userId);
    //print_log($result);
    //userRelicsVal 
    
    $this->setResponse('SUCCESS');
    return array('lb_list' => $result);
  }
}
