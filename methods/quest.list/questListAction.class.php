<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for cardGetMasterList Action
 */
class questListAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=quest.list", tags={"Quest"}, 
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
    $questLib = autoload::loadLibrary('queryLib', 'quest');
    $result = $cardId = $temp = array();

    // Get the List of all the Master Card
    $questList = $questLib->getMasterQuestDetail();
    foreach ($questList as $qlist)
    {
      $cardPropertyInfo = $temp = array();
      $temp['master_quest_id'] = $qlist['master_quest_id'];
      $temp['title'] = $qlist['title'];
      $temp['description'] = $qlist['description'];
      if($qlist['frequency'] == 0){
        $claim_freq = "Anytime";
      }elseif($qlist['frequency'] == 1){
        $claim_freq = "Once";
      }elseif($qlist['frequency'] == 2){
        $claim_freq = "Daily";
      }elseif($qlist['frequency'] == 3){
        $claim_freq = "Weekly";
      }elseif($qlist['frequency'] == 4){
        $claim_freq = "Monthly";
      }else{
        $claim_freq = "Anytime";
      }
      $temp['claim_freq'] = $claim_freq;
      $temp['crystal_reward_bonus'] = $qlist['crystal'];
      $temp['gold_reward_bonus'] = $qlist['gold'];
      $temp['trophy_reward_bonus']=$qlist['trophies'];
      $temp['current_slider_value']=0;
      $temp['slider_maxvalue']=($qlist['slide_maxvalue']>0)?$qlist['slide_maxvalue']:0;
      if($qlist['master_quest_id']==1){
        
      }
      $temp['is_achieved']=0;
      $temp['isclaimed']=0;
      $result[] = $temp;  
      
    }

    $this->setResponse('SUCCESS');
    return array('quest_list' => $result);
  }
}
