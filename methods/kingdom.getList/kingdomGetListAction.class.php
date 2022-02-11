<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for cardGetMasterList Action
 */
class kingdomGetListAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=kingdom.getList", tags={"Kingdom"}, 
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
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $kingdomLib->deleteKingdomUsersIfNotExists();
    $kingdomLib->deleteKingdomUsersMsgIfNotExists();
    $result = array();

    // Get the List of all the Master Card
    //$cardList = $cardLib->getMasterCardListWithStadium();
    $kingdomList=$kingdomLib->getKingdomListWithRank();
    print_log($kingdomList);
    foreach ($kingdomList as $kingdom)
    {
      $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelicsCount($kingdom['kingdom_id']);
      if($kingdomDetailsOnRelics > 0){
        $totalRelics=$kingdomLib->getKingdomTotalRelics($kingdom['kingdom_id']);
        $totalUserRelics= $kingdomLib->getKingdomRankRelicsTotal($kingdom['kingdom_id']);
        $totalUserTrophies= $kingdomLib->getKingdomUserTrophiesRelics($kingdom['kingdom_id']);
        $kingdomUserInfo = $temp = array();
        $temp['kingdom_id'] = $kingdom['kingdom_id']; 
        $temp['kingdom_name'] = $kingdom['kingdom_name'];
        $temp['kingdom_desc'] = $kingdom['kingdom_desc'];
        $temp['kingdom_shield_id'] = $kingdom['kingdom_shield_id'];
        $temp['kingdom_req_cup_amt'] = $kingdom['kingdom_req_cup_amt'];
        $temp['kingdom_location'] = $kingdom['kingdom_location'];
        $temp['kingdom_type'] = $kingdom['kingdom_type'];
        $temp['status'] = $kingdom['status'];
        $temp['actual_trophies'] = round($totalUserTrophies['trophies']);
        $temp['trophies']= round($totalUserRelics);
        $temp['total_donation']= $totalRelics['total_donation'];
        //$temp['kingdom_rank'] = $kingdom['srno'];
        
        
       /* $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelics($kingdom['kingdom_id']);
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
        
      
    }
    //$price = array_column($result, 'trophies');

    //array_multisort($price, SORT_DESC, $result);
    array_multisort(array_column($result, 'trophies'), SORT_DESC, $result);
    $i=1; $lv=null; $results=array();
    foreach ($result as $v) {
      $ranks = array();
      //if ($v>$lv){ $i++; $lv=$v;}
      $v['kingdom_rank']  = $i;
      $ranks=$v;
      $results[]=$ranks;
      $i++;
    }
    //return $ranks;
    $this->setResponse('SUCCESS');
    return array('kingdom_list' => $results);
  }
}
