<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 09-11-2020
 * Desc   : This is a controller file for kingdomCreate Action
 */
class kingdomCreateAction extends baseAction
{
	/**
   * @OA\Get(path="?methodName=kingdom.create", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="accept_user_id", name="accept_user_id", description="The accept_user_id specific to this event",
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
    //$roomLib = autoload::loadLibrary('queryLib', 'room');
    $kingdomLib = autoload::loadLibrary('queryLib', 'kingdom');
    $questLib = autoload::loadLibrary('queryLib', 'quest');
    //$inviteLib = autoload::loadLibrary('queryLib', 'invite');
    //$notificationLib = autoload::loadLibrary('queryLib', 'notification');
    $result = array();
    $userList = array();
    $userDetails = array();
    $waitngPlayerRoomId = $kingdomId = $roomId = 0;
    //Get the user Detail.
    $user = $userLib->getUserDetail($this->userId);
    $user_cnt = $kingdomLib->checkUserAvailable($this->userId);
    $kingdom_cnt = $kingdomLib->checkKingdomAlreadyExisted($this->kingdomName);
    //print_log("userCount:".$user_cnt);
    if($user_cnt == 0)
    {
      //$userVal = $userLib->getUserDetail($this->userId);
      if($user['relics'] >= $this->kingdomRequiedCups)
      {
        if($user['gold'] >= KINGDOM_GOLD_REQUIRED)
        {
          if($kingdom_cnt == 0)
          {
            //if matching player found then create a room and give room_id to the Matchingplayers.
            if(!empty($this->kingdomName))
            {
              $kingdomId = $kingdomLib->insertKingdom(array(
                  'kingdom_name' => $this->kingdomName,
                  'kingdom_type' => $this->kingdomType,
                  'kingdom_limit' => $this->kingdomLimit,
                  'kingdom_shield_id' => $this->shieldId,
                  'kingdom_desc' => $this->kingdomDesc,
                  'kingdom_location' => $this->kingdomLocation,
                  'kingdom_req_cup_amt' => $this->kingdomRequiedCups,
                  'created_at' => date('Y-m-d H:i:s'),
              ));
              if(!empty($kingdomId))
              {
                $kingdomUserId = $kingdomLib->insertKingdomUser(array(
                    'user_id' => $this->userId,
                    'user_type' => 2,
                    'avatar_url' => $user['avatar_url'],
                    'user_trophies' => $user['relics'],
                    'kingdom_id' => $kingdomId,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ));
                $userLib->updateUser($this->userId, array(
                    'kingdom_id' => $kingdomId,
                    'gold' => ($user['gold'] - KINGDOM_GOLD_REQUIRED)
                ));
              }
              $qv = $questLib->getQuestKingdomReward($this->userId, $this->androidVerId, $this->iosVerId);
              if(empty($qv)){
                $questLib->insertMasterQuestInventory(array(
                  'quest_id' => 5,
                  'time' => date('Y-m-d H:i:s'),
                  'user_id' => $this->userId,
                  'status' => CONTENT_ACTIVE,
                  'created_at' => date('Y-m-d H:i:s')));
              }
              $kingdomUsers = $kingdomLib->getKingdomUsersList($kingdomId);
              $kingdomDetailsOnRelics = $kingdomLib->getKingdomUserDetailsOnRelics($kingdomId);
              foreach ($kingdomDetailsOnRelics as $ku)
              {
                $userDetails = $userLib->getUserDetail($ku['user_id']);
                $tempUsers = array();
                $tempUsers['rank'] = $ku['srno'];
                $tempUsers['user_id'] = $ku['user_id'];
                $tempUsers['name'] = $userDetails['name'];
                $tempUsers['user_type'] = $ku['user_type'];
                $tempUsers['facebook_id'] = $userDetails['facebook_id'];
                $tempUsers['user_trophies'] = $userDetails['relics'];
                $tempUsers['donation'] = $ku['donation'];
                $userList[] = $tempUsers;
              }
              $kingdomStatus=1;
            }else{
                $this->setResponse('KINGDOM_NOT_CREATED');
                return new ArrayObject();
            }
          }else{
              $kingdomStatus=3;
              /*$this->setResponse('KINGDOM_NAME_ALREADY');
              return new ArrayObject();*/
          }
        }else{
            $kingdomStatus=4;
            /*$this->setResponse('INSUFFICIENT_GOLD');
            return new ArrayObject();*/
        }
      }else{
          $kingdomStatus=2;
          /*$this->setResponse('INSUFFICIENT_TROPHIES');
          return new ArrayObject();*/
      }
    }else{
        $kingdomStatus=5;
        /*$this->setResponse('KINGDOM_USER_ALREADY');
        return new ArrayObject();*/
    }
    $userV = $userLib->getUserDetail($this->userId);
    $result['kingdom_id'] = $kingdomId;
    $result['kingdom_name'] = $this->kingdomName;
    $result['kingdom_type'] = $this->kingdomType;
    $result['kingdom_limit'] = $this->kingdomLimit;
    $result['kingdom_shield_id'] = $this->shieldId;
    $result['kingdom_desc'] = $this->kingdomDesc;
    $result['kingdom_location'] = $this->kingdomLocation;
    $result['kingdom_req_cup_amt'] = $this->kingdomRequiedCups;
    $result['kingdom_success'] = $kingdomStatus;
    $result['user_total_gold'] = $userV['gold'];
    $result['kingdom_user_desc'] = "User Type Code = 0 : Requested, 1 : Member, 2 : Admin";
    //$userList[]=$userDetails;
    $result['kingdom_userlist'] = $userList;

    $this->setResponse('SUCCESS');
    return $result;
  }
}
