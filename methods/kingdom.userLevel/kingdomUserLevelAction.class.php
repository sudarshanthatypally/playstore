<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 09-11-2020
 * Desc   : This is a controller file for kingdomCreate Action
 */
class kingdomUserLevelAction extends baseAction
{
	/**
   * @OA\Get(path="?methodName=kingdom.userLevel", tags={"Kingdom"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="level_user_id", name="level_user_id", description="The level_user_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="level_type", name="level_type", description="The level_type specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kick_msg", name="kick_msg", description="The kick_msg specific to this event",
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
    $userList = $requestedUserList =$kickedUserList= array();
    $userDetails = array();
    $waitngPlayerRoomId = $kingdomId = $roomId = 0;
    //Get the user Detail.
    $user = $userLib->getUserDetail($this->userId);
    $user_cnt = $kingdomLib->checkUserAvailable($this->userId);
    //$kingdom_cnt = $kingdomLib->checkKingdomAlreadyExisted($this->kingdomName);
    $kuDetails= $kingdomLib->getKingdomUserDetailsWithUsersId($this->levelUserId);
    $requesterDetails= $kingdomLib->getKingdomUserDetailsWithUsersId($this->userId);
    $kingdomDetails= $kingdomLib->getKingdomDetails($requesterDetails['kingdom_id']);
    $sender = $userLib->getUserDetail($this->userId);
    $receiver = $userLib->getUserDetail($this->levelUserId);
    $sname = empty($sender['name'])?"Guest_".$this->userId:$sender['name'];
    $rname = empty($receiver['name'])?"Guest_".$this->levelUserId:$receiver['name'];
    $receiverExists = $kingdomLib->checkUserExists($this->levelUserId, $kuDetails['kingdom_id']);
    if(!empty($receiverExists)){
      if($kuDetails['kingdom_id'] == $requesterDetails['kingdom_id']){
        if(!empty($kuDetails)){  
          
          switch ($requesterDetails['user_type']) {
            case 0:
              $userTypeMsg= "User not valid";
              break;
            case 1:
              $userTypeMsg= "User not valid";
              break;
            case 4:
              $userTypeMsg= "User not valid";
              break;
            case 3:
              if($this->levelType ==1){
                if($kuDetails['user_type']==1){
                  $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array(
                    'user_type' => 4
                  ));
                  $userTypeMsg= "User Promoted";
                  $msgId = $kingdomLib->insertKingdomMsg(array(
                    'kingdom_id' => $requesterDetails['kingdom_id'],
                    'sent_by' => $this->userId,
                    'received_by' => $this->levelUserId,
                    'msg_type' => 8,
                    'chat_type' => 2,
                    'message' => $rname." was promoted by ".$sname,
                    'created_at' => date('Y-m-d H:i:s')
                  ));
                }else{
                  $userTypeMsg= "User not valid to Promote/Demote";
                }  
              }elseif($this->levelType ==2){
                if($kuDetails['user_type']==4){
                  $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array(
                    'user_type' => 1
                  ));
                  $userTypeMsg= "User Demoted";
                  $msgId = $kingdomLib->insertKingdomMsg(array(
                    'kingdom_id' => $requesterDetails['kingdom_id'],
                    'sent_by' => $this->userId,
                    'received_by' => $this->levelUserId,
                    'msg_type' => 9,
                    'chat_type' => 2,
                    'message' => $rname." was demoted by ".$sname,
                    'created_at' => date('Y-m-d H:i:s')
                  ));
                }else{
                  $userTypeMsg= "User not valid to Promote/Demote";
                }  
              }elseif($this->levelType ==3){
                $msg=trim($this->kickMsg);
                $msg=htmlentities($msg);
                $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array(
                  'user_type' => 9,
                  'kick_msg'=>$msg
                ));
                $msgId = $kingdomLib->insertKingdomMsg(array(
                  'kingdom_id' => $requesterDetails['kingdom_id'],
                  'sent_by' => $this->userId,
                  'received_by' => $this->levelUserId,
                  'msg_type' => 10,
                  'chat_type' => 2,
                  'message' => $rname." has been kicked out of the Kingdom by ".$sname,
                  'created_at' => date('Y-m-d H:i:s')
                ));
                $userTypeMsg= "User Kicked Off";
              }elseif($this->levelType ==9){
                $kingdomLib->deleteKingdomUser($this->levelUserId);
                $userLib->updateUser($this->levelUserId, array(
                  'kingdom_id' => 0
                ));
                $userTypeMsg= "User removed";
              }else{
                $userTypeMsg= "User Level should be 1 or 2";
              }
              break;
            case 2:
                if($this->levelType ==1){
                  if($kuDetails['user_type']==1){
                    $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array(
                      'user_type' => 4
                    ));
                    $userTypeMsg= "User Promoted";
                    $msgId = $kingdomLib->insertKingdomMsg(array(
                      'kingdom_id' => $requesterDetails['kingdom_id'],
                      'sent_by' => $this->userId,
                      'received_by' => $this->levelUserId,
                      'msg_type' => 8,
                      'chat_type' => 2,
                      'message' => $rname." was promoted by ".$sname,
                      'created_at' => date('Y-m-d H:i:s')
                    ));
                  }elseif($kuDetails['user_type']==4){
                    $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array(
                      'user_type' => 3
                    ));
                    $userTypeMsg= "User Promoted";
                    $msgId = $kingdomLib->insertKingdomMsg(array(
                      'kingdom_id' => $requesterDetails['kingdom_id'],
                      'sent_by' => $this->userId,
                      'received_by' => $this->levelUserId,
                      'msg_type' => 8,
                      'chat_type' => 2,
                      'message' => $rname." was promoted by ".$sname,
                      'created_at' => date('Y-m-d H:i:s')
                    ));
                  }elseif($kuDetails['user_type']==3){
                    $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array('user_type' => 2));
                    $kingdomLib->updateKingdomUser($this->userId, $requesterDetails['kingdom_id'], array('user_type' => 3));
                    $userTypeMsg= "Co-Leader User Promoted and Leader became Co-Leader";
                    $msgId = $kingdomLib->insertKingdomMsg(array(
                      'kingdom_id' => $requesterDetails['kingdom_id'],
                      'sent_by' => $this->userId,
                      'received_by' => $this->levelUserId,
                      'msg_type' => 8,
                      'chat_type' => 2,
                      'message' => $rname." was promoted by ".$sname,
                      'created_at' => date('Y-m-d H:i:s')
                    ));
                  }else{
                    $userTypeMsg= "User not valid to Promote/Demote";
                  }  
                }elseif($this->levelType ==2){
                  if($kuDetails['user_type']==3){
                    $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array(
                      'user_type' => 4
                    ));
                    $userTypeMsg= "User Demoted";
                    $msgId = $kingdomLib->insertKingdomMsg(array(
                      'kingdom_id' => $requesterDetails['kingdom_id'],
                      'sent_by' => $this->userId,
                      'received_by' => $this->levelUserId,
                      'msg_type' => 9,
                      'chat_type' => 2,
                      'message' => $rname." was demoted by ".$sname,
                      'created_at' => date('Y-m-d H:i:s')
                    ));
                  }elseif($kuDetails['user_type']==4){
                    $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array(
                      'user_type' => 1
                    ));
                    $userTypeMsg= "User demoted"; 
                    $msgId = $kingdomLib->insertKingdomMsg(array(
                      'kingdom_id' => $requesterDetails['kingdom_id'],
                      'sent_by' => $this->userId,
                      'received_by' => $this->levelUserId,
                      'msg_type' => 9,
                      'chat_type' => 2,
                      'message' => $rname." was demoted by ".$sname,
                      'created_at' => date('Y-m-d H:i:s')
                    ));
                  }else{
                    $userTypeMsg= "User not valid to Promote/Demote";
                  }  
                }elseif($this->levelType ==3){
                  $msg=trim($this->kickMsg);
                  $msg=htmlentities($msg);
                  $kingdomLib->updateKingdomUser($this->levelUserId,$requesterDetails['kingdom_id'], array(
                    'user_type' => 9,
                    'kick_msg'=>$msg
                  ));
                  $userTypeMsg= "User Kicked Off";
                  $msgId = $kingdomLib->insertKingdomMsg(array(
                    'kingdom_id' => $requesterDetails['kingdom_id'],
                    'sent_by' => $this->userId,
                    'received_by' => $this->levelUserId,
                    'msg_type' => 10,
                    'chat_type' => 2,
                    'message' => $rname." has been kicked out of the Kingdom by ".$sname,
                    'created_at' => date('Y-m-d H:i:s')
                  ));
                }elseif($this->levelType ==9){
                  $kingdomLib->deleteKingdomUser($this->levelUserId);
                  $userTypeMsg= "User removed";
                }else{
                  $userTypeMsg= "User Level should be 1 or 2";
                }
              break;
            case 9:
              $kingdomLib->deleteKingdomUser($this->levelUserId);
              $userLib->updateUser($this->levelUserId, array(
                'kingdom_id' => 0
              ));
              $userTypeMsg= "User removed";
              break;
            default:
              echo "User not valid to Promote or Demote";
          }
          $kingdomStatus=1;
        }else{
          $this->setResponse('CUSTOM_ERROR', array('error'=>'Accepting User Id not valid..'));
          return new ArrayObject();
        }
      }else{
        $this->setResponse('CUSTOM_ERROR', array('error'=>'Kingdom Id is not valid..'));
        return new ArrayObject();
      }
    }else{
      $kingdomStatus=7; 
    }
    $userDetails = $userLib->getUserDetail($this->levelUserId);
    $kingdomDetailsOnRelics= $kingdomLib->getKingdomUserDetailsOnRelics($requesterDetails['kingdom_id']);
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
    if($requesterDetails['user_type']>=2){
      $kingdomRequestedDetailsOnRelics = $kingdomLib->getKingdomUserRequestedDetailsOnRelics($requesterDetails['kingdom_id']);
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
    /*$tempUsers = array();
    //$tempUsers['rank'] = $ku['srno'];
    $tempUsers['user_id'] = $kuDetails['user_id'];
    $tempUsers['name'] = $userDetails['name'];
    $tempUsers['user_type'] = $kuDetails['user_type'];
    $tempUsers['facebook_id'] = $userDetails['facebook_id'];
    $tempUsers['user_trophies'] = $userDetails['relics'];
    $tempUsers['user_total_gold'] = $userDetails['gold'];
    $tempUsers['donation'] = $kuDetails['donation'];
    $userList[] = $tempUsers;
    */
     
    $result['kingdom_id'] = $requesterDetails['kingdom_id'];
    $result['kingdom_name'] = $kingdomDetails['kingdom_name'];
    $result['kingdom_type'] = $kingdomDetails['kingdom_type'];
    $result['kingdom_member_limit'] = $kingdomDetails['kingdom_limit'];
    $result['kingdom_shield_id'] = $kingdomDetails['kingdom_shield_id'];
    $result['kingdom_desc'] = $kingdomDetails['kingdom_desc'];
    $result['kingdom_location'] =  $kingdomDetails['kingdom_location'];
    $result['kingdom_req_cup_amt'] = $kingdomDetails['kingdom_req_cup_amt'];
    $result['kingdom_success'] = $kingdomStatus;
    $result['user_level_msg'] = $userTypeMsg;
    $result['kingdom_user_desc']="User Type Code = 0 : Requested, 1 : Member, 2 : Admin, 3: Co-Leader, 4: Elder";
    //$userList[]=$userDetails;
    $result['kingdom_userlist']=$userList;
    /*
    if($requesterDetails['user_type']>=2){
      $result['kingdom_requested_userlist']=$requestedUserList;
    }*/
    if($requesterDetails['user_type']>=2){
      $result['kingdom_requested_userlist']=$requestedUserList;
      $result['kingdom_kicked_userlist']=$kickedUserList;
    }
 
    $this->setResponse('SUCCESS');
    return $result;
  }
}

