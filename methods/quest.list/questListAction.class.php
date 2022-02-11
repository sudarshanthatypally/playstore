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
   * @OA\Parameter(parameter="android_version_id", name="android_version_id", description="The android_version_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="ios_version_id", name="ios_version_id", description="The ios_version_id specific to this event",
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
    $result = $cardId = array();//$temp = 
    date_default_timezone_set("Asia/Kolkata");
    // Get the List of all the Master Card
    $questList = $questLib->getMasterQuestDetail();
    foreach ($questList as $qlist)
    {
      $cardPropertyInfo = $temp = array();
      
      
      
      if($qlist['master_quest_id']==1){
        $temp['slider_maxvalue']=($qlist['slide_maxvalue']>0)?$qlist['slide_maxvalue']:0;
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
        $seconds = strtotime('tomorrow') - time();
        $temp['remaining_time'] = $seconds; 
        $qv = $questLib->getQuestCollectFreeReward($this->userId, $this->androidVerId, $this->iosVerId);
        $qc = $questLib->getQuestCollectFreeRewardClaimed($this->userId);
        if(!empty($qc)){
          $temp['isclaimed']=1; //0 available to claimed, 1 claimed, 2 not eligible
          $temp['current_slider_value']=1; 
        }else{
          if(!empty($qv)){
            $temp['current_slider_value']=1;
            $temp['isclaimed']=0;
          }else{
            $temp['current_slider_value']=0;
            $temp['isclaimed']=2;
          }
        }
      }
      /*else{
        $temp['current_slider_value']=0;
        $temp['isclaimed']=2;
      }*/

      if($qlist['master_quest_id']==2){
        $temp['slider_maxvalue']=($qlist['slide_maxvalue']>0)?$qlist['slide_maxvalue']:0;
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
        $seconds = strtotime('next monday') - time();
        $temp['remaining_time'] = $seconds; 
        $qv = $questLib->getQuestPatBattle100Reward($this->userId, $this->androidVerId, $this->iosVerId);
        $qc = $questLib->getPlayBattle100QuestClaimed($this->userId);
        if(!empty($qc)){
          $temp['isclaimed']=1;  //0 available to claimed, 1 claimed, 2 not eligible
          $temp['current_slider_value']=!empty($qv['match_count'])?$qv['match_count']:0;
        }else{
          if(!empty($qv)){
            $temp['current_slider_value']=!empty($qv['match_count'])?$qv['match_count']:0;
            if($temp['slider_maxvalue']<=$temp['current_slider_value']){
              $temp['isclaimed']=0;
            }else{
              $temp['isclaimed']=2;
            }
          }else{
            $temp['current_slider_value']=0;
            $temp['isclaimed']=2;
          }
        }
      }
      $qv100 = $questLib->getQuestPatBattle100Reward($this->userId, $this->androidVerId, $this->iosVerId);
      $qv200 = $questLib->getQuestPlayBattle200Reward($this->userId, $this->androidVerId, $this->iosVerId);
      $qv500 = $questLib->getQuestPlayBattle500Reward($this->userId, $this->androidVerId, $this->iosVerId);
     
     if(!empty($qv100['match_count']) || !empty($qv200['match_count']) ){
        if($qv100['slide_maxvalue']<=$qv100['match_count']){
          if($qlist['master_quest_id']==10){
            $temp['slider_maxvalue']=($qlist['slide_maxvalue']>0)?$qlist['slide_maxvalue']:0;
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
            $qv = $questLib->getQuestPlayBattle200Reward($this->userId, $this->androidVerId, $this->iosVerId);
            $qc = $questLib->getPlayBattle200QuestClaimed($this->userId);
            if(!empty($qc)){
              $temp['isclaimed']=1;  //0 available to claimed, 1 claimed, 2 not eligible
              $temp['current_slider_value']=$qv['match_count'];
            }else{
              if(!empty($qv)){
                $temp['current_slider_value']=$qv['match_count'];
                if($temp['slider_maxvalue']<=$temp['current_slider_value']){
                  $temp['isclaimed']=0;
                }else{
                  $temp['isclaimed']=2;
                }
              }else{
                $temp['current_slider_value']=0;
                $temp['isclaimed']=2;
              }
            }
          }
        }
     }
      
      if(!empty($qv200['match_count']) || !empty($qv500['match_count'])){
        if($qv200['slide_maxvalue']<=$qv200['match_count']){
          if($qlist['master_quest_id']==11){
            $temp['slider_maxvalue']=($qlist['slide_maxvalue']>0)?$qlist['slide_maxvalue']:0;
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
            $qv = $questLib->getQuestPlayBattle500Reward($this->userId, $this->androidVerId, $this->iosVerId);
            $qc = $questLib->getPlayBattle500QuestClaimed($this->userId);
            if(!empty($qc)){
              $temp['isclaimed']=1;  //0 available to claimed, 1 claimed, 2 not eligible
              $temp['current_slider_value']=$qv['match_count'];
            }else{
              if(!empty($qv)){
                $temp['current_slider_value']=$qv['match_count'];
                if($temp['slider_maxvalue']<=$temp['current_slider_value']){
                  $temp['isclaimed']=0;
                }else{
                  $temp['isclaimed']=2;
                }
              }else{
                $temp['current_slider_value']=0;
                $temp['isclaimed']=2;
              }
            }
          }
        }
      }
      /*else{
        $temp['current_slider_value']=0;
        $temp['isclaimed']=2;
      }*/
      /*else{
        $temp['current_slider_value']=0;
        $temp['isclaimed']=2;
      }*/
      /*else{
        $temp['current_slider_value']=0;
        $temp['isclaimed']=2;
      }*/
      if($qlist['master_quest_id']==3){
        $temp['slider_maxvalue']=($qlist['slide_maxvalue']>0)?$qlist['slide_maxvalue']:0;
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
        $qv = $questLib->getQuestKathikaReward($this->userId, $this->androidVerId, $this->iosVerId);
        $qc = $questLib->getKathikaQuestClaimed($this->userId);
        $qvk = $questLib->getQuestKathikaRewardInKathika($this->userId);
        if(!empty($qc)){
          $temp['isclaimed']=1;  //0 available to claimed, 1 claimed, 2 not eligible
          $temp['current_slider_value']=1;
        }else{
          if(!empty($qvk) || !empty($qv)){
            $temp['current_slider_value']=1;
            $temp['isclaimed']=0;
          }else{
            $temp['current_slider_value']=0;
            $temp['isclaimed']=2;
          }
        }
      }
      /*else{
        $temp['current_slider_value']=0;
        $temp['isclaimed']=2;
      }*/
//|| $qlist['master_quest_id']==10 || $qlist['master_quest_id']==11
      
      //if(!is_null($temp)){
        $result[] = $temp; 
      
       
      
    }

    $this->setResponse('SUCCESS');
    return array('quest_list' => array_filter($result));
  }
}
