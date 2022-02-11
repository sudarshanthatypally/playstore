<?php
/**
 * Author : Abhijth Shetty
 * Date   : 16-03-2018
 * Desc   : This is a controller file for questClaim Action
 */
 /**
 * @OA\Server(url="http://35.176.252.22/EPIKO/staging/rest.php")
 * @OA\Info(title="Epiko Regal", version="1.0",
 * @OA\Contact(
 *     email="sudarshant@wharfstreetstudios.com"
 *   )
 * )
 */
class questClaimAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=quest.claim", tags={"Quest"}, 
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
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
    $questLib = autoload::loadLibrary('queryLib', 'quest');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $result = array();

    if($this->questId==1){
      $qc = $questLib->getQuestCollectFreeRewardClaimed($this->userId);
      if(!empty($qc)){
        $this->setResponse('CUSTOM_ERROR', array('error'=>'You already claimed the reward'));
        return new ArrayObject();
      }
    }
    if($this->questId==2){
      $qc = $questLib->getPlayBattle100QuestClaimed($this->userId);
      $qv = $questLib->getQuestPatBattle100Reward($this->userId);
      $result['current_slider_value']=$qv['match_count'];
      if(!empty($qc)){
        $this->setResponse('CUSTOM_ERROR', array('error'=>'You already claimed the reward'));
        return new ArrayObject();
      }/*else{
        $questD = $questLib->getQuestDetails($this->questId);
        $questLib->updateQuestInventory($this->questId, $this->userId,array('match_count' => ($questD['match_count']-100)));
      }*/
    }
    if($this->questId==10){
      $qc = $questLib->getPlayBattle200QuestClaimed($this->userId);
      $qv = $questLib->getQuestPlayBattle200Reward($this->userId);
      $result['current_slider_value']=$qv['match_count'];
      if(!empty($qc)){
        $this->setResponse('CUSTOM_ERROR', array('error'=>'You already claimed the reward'));
        return new ArrayObject();
      }
    }
    if($this->questId==11){
      $qc = $questLib->getPlayBattle500QuestClaimed($this->userId);
      $qv = $questLib->getQuestPlayBattle500Reward($this->userId);
      $result['current_slider_value']=$qv['match_count'];
      if(!empty($qc)){
        $this->setResponse('CUSTOM_ERROR', array('error'=>'You already claimed the reward'));
        return new ArrayObject();
      }
    }
    if($this->questId==3){
      $qc = $questLib->getKathikaQuestClaimed($this->userId);
      if(!empty($qc)){
        $this->setResponse('CUSTOM_ERROR', array('error'=>'You already claimed the reward'));
        return new ArrayObject();
      }
    }
    $userDetail = $userLib->getUserDetail($this->userId);
    $questDetails = $questLib->getQuestDetails($this->questId);
    $claimId= $questLib->insertMasterQuestClaimed(array(
      'user_id' => $this->userId, 
      'title' => $questDetails['title'],
      'quest_id' => $this->questId,
      'gold' => $questDetails['gold'],
      'crystal' => $questDetails['crystal'],
      'trophies' => $questDetails['trophies'],
      'status' => $questDetails['status'],
      'created_at' => date('Y-m-d H:i:s')));

    $userLib->updateUser($this->userId, array('gold' => $userDetail['gold']+ $questDetails['gold'], 'crystal' => $userDetail['crystal']+ $questDetails['crystal']));
    $userDetail = $userLib->getUserDetail($this->userId);
    $result["master_quest_id"]= $this->questId;
    $result["title"]= $questDetails['title'];
    $result["crystal_reward_bonus"]= $questDetails['crystal'];
    $result["gold_reward_bonus"]= $questDetails['gold'];
    $result["trophy_reward_bonus"]= $questDetails['trophies'];
    $result["total_gold"]= $userDetail['gold'];
    $result["total_crystal"]= $userDetail['crystal'];
    //$result["current_slider_value"]= 0;
    //$result["slider_maxvalue"]= 1;
    if($questDetails['frequency'] == 0){
      $claim_freq = "Anytime";
    }elseif($questDetails['frequency'] == 1){
      $claim_freq = "Once";
    }elseif($questDetails['frequency'] == 2){
      $claim_freq = "Daily";
    }elseif($questDetails['frequency'] == 3){
      $claim_freq = "Weekly";
    }elseif($questDetails['frequency'] == 4){
      $claim_freq = "Monthly";
    }else{
      $claim_freq = "Anytime";
    }
    $result["claim_freq"]= $claim_freq;
    $result["isclaimed"]=1;
    $result['created_at'] = date('Y-m-d H:i:s');


    $this->setResponse('SUCCESS');
    return $result;
  }
}
