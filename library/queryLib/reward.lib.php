<?php
class reward{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function checkEligibilityOfCopperReward($userId, $stadiumId,  $options=array())
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $roomLib = autoload::loadLibrary('queryLib', 'room');
    $userCircletcount = array();

    $userRecentCopperRewardDetail = $rewardLib->getLastFreeCubeRewardDetailForUser($userId, (CUBE_DYNAMITE));
    $unlockTime = (($userRecentCopperRewardDetail['claimed_at']) + UNLOCK_CUBE_DYNAMITE_TIMEOUT);

    if((($unlockTime)-time() <= 0 && ($userRecentCopperRewardDetail['status'] == CONTENT_CLOSED)) || (empty($userRecentCopperRewardDetail))){
      $userCircletcount = $roomLib->getUserRewardCircletCount($userId,  $unlockTime);
    }

    if(!empty($userCircletcount) && $userCircletcount['sum_of_circlet'] >= CIRCLET_COUNT )
    {
      $cubeId = CUBE_DYNAMITE;
      $userLib->insertUserReward(array(
                  'user_id' =>$userId,
                  'cube_id' => $cubeId,
                  'master_stadium_id' => $stadiumId,
                  'created_at' => date('Y-m-d H:i:s'),
                  'status' => CUBE_CAN_BE_CLAIMED));
    }
    return array('total_circlet'=> !empty($userCircletcount)?$userCircletcount['sum_of_circlet']:0, 'unlock_time'=>($unlockTime)-time());
  }

  public function claimCopperCubeReward($userId,  $userRewardId, $claimReward)
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $result = array();

    $userReward = $userLib->getUserRewardDetail($userRewardId);
    $userDetail = $userLib->getUserDetail($userId);

    $result['cube_id'] = $userReward['cube_id'];
    $result['master_stadium_id'] = $userReward['master_stadium_id'];
    $result['reward_status'] = $userReward['status'];
    $result['crystal_bonus'] = COPPER_CUBE_CRYSTAL_COUNT;

    if($claimReward > 0)
    {
      $result['reward_status'] = CONTENT_CLOSED;

      $userLib->updateUserReward($userRewardId, array('claimed_at' => time(), 'status' => CONTENT_CLOSED));
      $userLib->updateUser($userId,  array('crystal' => $userDetail['crystal'] + COPPER_CUBE_CRYSTAL_COUNT,'is_copper_cube_notification_sent'=>CONTENT_ACTIVE));

    }

    return $result;
  }

  public function claimBronzeCubeReward($userId,  $userRewardId, $claimReward)
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $cardLib = autoload::loadLibrary('queryLib', 'card');

    $result = array();

    $userReward = $userLib->getUserRewardDetail($userRewardId);
    $userDetail = $userLib->getUserDetail($userId);

    $result['cube_id'] = $userReward['cube_id'];
    $result['reward_status'] = $userReward['status'];
    $result['master_stadium_id'] = $userReward['master_stadium_id'];

    if($claimReward > 0)
    {
      $commonCardList = $cardLib->getMasterCardRarityListBasedOnStadium($userReward['master_stadium_id'], CARD_RARITY_COMMON);

      $randomCard = rand(0, count($commonCardList)-1);

      if(rand(0, 1))
      {
        $result['total_card_in_cube'] = 1;
        $commonCard = $commonCardList[$randomCard]['master_card_id'];
        $result['card_details'][] =  $rewardLib->addRewardedCard($userId, $commonCard, DEFAULT_CARD_COUNT);
      } else
      {
        $result['crystal_bonus'] = BRONZE_CUBE_CRYSTAL_COUNT;
        $userLib->updateUser($userId, array('crystal' => $userDetail['crystal'] + BRONZE_CUBE_CRYSTAL_COUNT));
      }

      $result['reward_status'] = CONTENT_CLOSED;
      $userLib->updateUserReward($userRewardId,  array('claimed_at' => time(), 'status' => CONTENT_CLOSED));
    }

    return $result;
  }

  public function claimCubeRewardedDuringMatch($userId, $userRewardId, $claimReward, $andVer, $iosVer, $claimCrystalUpgrade=array())
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $cubeLib = autoload::loadLibrary('queryLib', 'cube');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');

    $result = array();
    $userReward = $userLib->getUserRewardDetail($userRewardId);
    $userDetail = $userLib->getUserDetail($userId);

    $cubeRewardDetail = $cubeLib->getCubeRewardDetailForStadium($userReward['cube_id'], $userReward['master_stadium_id']);
    /*$maxTime = ($userReward['cube_id'] == CUBE_FIRECRACKER)?UNLOCK_CUBE_FIRECRACKER_TIMEOUT:(($userReward['cube_id'] == CUBE_BOMB)?UNLOCK_CUBE_BOMB_TIMEOUT:(($userReward['cube_id'] == CUBE_METALBOMB) ? UNLOCK_CUBE_METALBOMB_TIMEOUT : UNLOCK_CUBE_ROCKET_TIMEOUT));
    $favcolor = "red";*/ 

    switch ($userReward['cube_id']) {
      case CUBE_FIRECRACKER:
       $mTime = UNLOCK_CUBE_FIRECRACKER_TIMEOUT;
        break;
      case CUBE_BOMB:
        $mTime = UNLOCK_CUBE_BOMB_TIMEOUT;
        break;
      case CUBE_ROCKET:
        $mTime = UNLOCK_CUBE_ROCKET_TIMEOUT;
        break;
      case CUBE_DYNAMITE:
        $mTime = UNLOCK_CUBE_DYNAMITE_TIMEOUT;
        break;
      case CUBE_METALBOMB:
        $mTime = UNLOCK_CUBE_METALBOMB_TIMEOUT;
        break;
      default:
        break;
    }

    $maxTime = $mTime;

    $temp_time= strtotime(date("Y-m-d H:i:s", strtotime('+'.$maxTime.' hours', $userReward['claimed_at'])));
    $result['reward_unlock_time'] = (($userReward['claimed_at'] == 0) || (($temp_time) - time()<0))?0:(($temp_time) - time());  

    //$result['reward_unlock_time'] = (($userReward['claimed_at'] == 0) || (($userReward['claimed_at']+$maxTime) - time()<0))?0:(($userReward['claimed_at']+$maxTime) - time());
    $result['reward_status'] = ($userReward['status'] ==  CUBE_ON_PROCESS && $result['reward_unlock_time'] <= 0)?CUBE_CAN_BE_CLAIMED:$userReward['status'];
    $result['cube_id'] = $userReward['cube_id'];
    $crystalDetail = $cubeLib->getCubeRewardDetailForStadium($userReward['cube_id'], $userReward['master_stadium_id']);   
   /* if($userReward['status'] ==  CUBE_ON_PROCESS){
      if($userDetail['crystal'] < $crystalDetail['crystal_cost'])
      {
        $this->setResponse('CRYSTAL_IS_NOT_ENOUGH');
        return new ArrayObject();
      }
    }else{*/
      if($claimCrystalUpgrade['cube_upgrade_id']==3){
        $crystalDetail = $cubeLib->getCubeRewardDetailForStadium($userReward['cube_id'], $userReward['master_stadium_id']);  
        $crystalVal = $userDetail['crystal']-$crystalDetail['crystal_cost']; 
        $result['reward_status']= $userReward['status'] =  CUBE_CAN_BE_CLAIMED;
      }
   // }
    
    //if cube active and player claimed then start the unlocking timer
    if($userReward['status'] ==  CUBE_ON_PROCESS &&  $result['reward_unlock_time'] <= 1)
    {
      $result['reward_unlock_time'] = $result['reward_unlock_time'];
      $result['reward_status'] = ($result['reward_unlock_time'] <= 0)?CUBE_CAN_BE_CLAIMED:CUBE_ON_PROCESS;
      $userLib->updateUserReward($userRewardId, array('status' => CUBE_CAN_BE_CLAIMED));
    }

    if($claimReward > 0)
    {
      //if cube active and player claimed then start the unlocking timer
      if($userReward['status'] ==  CUBE_ACTIVE )
      {
        //$result['reward_unlock_time'] = $maxTime;
        if(!empty($userReward['claimed_at']) && $userReward['claimed_at'] != 0){
          $temp_time= strtotime(date("Y-m-d H:i:s", strtotime('+'.$maxTime.' hours', $userReward['claimed_at'])));
          $result['reward_unlock_time'] = (($userReward['claimed_at'] == 0) || (($temp_time) - time()<0))?0:(($temp_time) - time());
        }else{
          $temp_time= strtotime(date("Y-m-d H:i:s", strtotime('+'.$maxTime.' hours', time())));  
          $result['reward_unlock_time'] = (($temp_time == 0) || (($temp_time) - time()<0))?0:(($temp_time) - time());
        }
          
        $result['reward_status'] = CUBE_ON_PROCESS;
        $userLib->updateUserReward($userRewardId, array('claimed_at' => time(), 'status' => CUBE_ON_PROCESS));
      }

      //player unlocked the card
      if($userReward['status'] ==  CUBE_CAN_BE_CLAIMED || ($result['reward_unlock_time'] <= 0 && $userReward['status'] ==  CUBE_ON_PROCESS))
      {
        $result['reward_unlock_time'] = 0;
        $cardIdList = $rareCardList = $ultraRareCardList = $cardList = array();
        $cardCount = 0;
        //$cardIdList = $rewardLib->getRandomCard($cubeRewardDetail, $userReward['master_stadium_id']);
        $cardIdList = $rewardLib->getRandomCardWithVersion($cubeRewardDetail, $userReward['master_stadium_id'], $andVer, $iosVer);
        

        foreach($cardIdList as $cardId => $cardIdVal){
          $cardList[] = $rewardLib->addRewardedCard($userId, $cardId, $cardIdVal);
        }

        //$userLib->updateUser($userId, array('gold' => $userDetail['gold']+ $cubeRewardDetail['gold']));
        //$userLib->updateUser($userId,  array('crystal' => $userDetail['crystal'] + COPPER_CUBE_CRYSTAL_COUNT,'is_copper_cube_notification_sent'=>CONTENT_ACTIVE));
        $userDetail = $userLib->getUserDetail($userId);
        $crystalDetail = $cubeLib->getCubeRewardDetailForStadium($userReward['cube_id'], $userReward['master_stadium_id']);  
        if(!empty($crystalDetail['crystal']))
        {
          if($claimCrystalUpgrade['cube_upgrade_id']==3){
            $totalCrystal = $crystalVal + $crystalDetail['crystal']; 
          }else{
            $totalCrystal= $userDetail['crystal']+$crystalDetail['crystal'];
          }
          //$userDetail['crystal'];
        }


        $userLib->updateUser($userId, array('gold' => $userDetail['gold']+ $cubeRewardDetail['gold'], 'crystal' => $totalCrystal));
        $result['card_details'] = $cardList;
        $result['reward_status'] = CONTENT_CLOSED;
        $userLib->updateUserReward($userRewardId, array('claimed_at' => time(), 'status' => CONTENT_CLOSED));
        $userReward = $userLib->getUserRewardDetail($userRewardId);
      }
    }
    $userDetail = $userLib->getUserDetail($userId);
    $result['master_stadium_id'] = $userReward['master_stadium_id'];
    $result['crystal']=$userDetail['crystal'];
    //$result['crystal']=$cubeRewardDetail['crystal'];
    if(!empty($cubeRewardDetail)){
      $result['gold_bonus'] = $cubeRewardDetail['gold'];
      $result['crystal_bonus'] = $cubeRewardDetail['crystal'];
      $result['total_card_in_cube'] = $cubeRewardDetail['card_count'];

      if($cubeRewardDetail['rare'] > 0){
        $result['total_rare_card_in_cube'] = $cubeRewardDetail['rare'];
      }
      if($cubeRewardDetail['epic'] > 0){
        $result['total_epic_card_in_cube'] = $cubeRewardDetail['epic'];
      }
      if($cubeRewardDetail['ultra_epic'] > 0){
        $result['total_ultra_epic_card_in_cube'] = $cubeRewardDetail['ultra_epic'];
      }

    }

    return $result;
  } 

  public function getSplitAfterCalculationValues($totalValue, $numOfSplitValues, $minSplitValue)
  {
    $number_of_splits   = $numOfSplitValues;
    $sum_to             = $totalValue;
    $splits             = array();
    $split_values       = 0;

    while(array_sum($splits) != $sum_to)
    {
        if($minSplitValue < 24 && $number_of_splits > 1){
          $splits[$split_values] = mt_rand(1, $sum_to/mt_rand(1,$number_of_splits));
        }
        else{
          $splits[$split_values] = mt_rand($minSplitValue, $sum_to/mt_rand(1,$number_of_splits));
        }
        if(++$split_values == $number_of_splits)
        {
            $split_values  = 0;
        }
    }
    return $splits;
  } 
  function combine_arr($a, $b)
  {
      $acount = count($a);
      $bcount = count($b);
      $size = ($acount > $bcount) ? $bcount : $acount;
      $a = array_slice($a, 0, $size);
      $b = array_slice($b, 0, $size);
      return array_combine($a, $b);
  }

  public function getLastCubeRewardDetailForUser($userId, $cubeId, $options=array())
  {
    $sql = "SELECT created_at, status, claimed_at
            FROM user_reward
            WHERE user_id = :userId AND cube_id = :cubeId
            ORDER BY created_at DESC";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'cubeId' => $cubeId));
    return $result;
  }

  public function addRewardedCard($userId, $cardId, $cardCount)
  {
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $userLib = autoload::loadLibrary('queryLib', 'user');

    $masterCard = $cardLib->getMasterCardDetail($cardId);
    $masterCardPropertyList = $cardLib->getMasterCardPropertyList($cardId);

    $userCard = $cardLib->getUserCardDetailForMasterCardId($userId, $cardId);
    $userDetail = $userLib->getUserDetail($userId);
    $temp = array();

    $temp["master_card_id"] = $cardId;
    $temp['title'] = $masterCard["title"];
    $temp['card_level'] = $cardLevelId = (empty($userCard['level_id']))?DEFAULT_CARD_LEVEL_ID:$userCard["level_id"];
    //$userCardCount = $cardCount;
    if(empty($userCard['user_card_count'])){
      $userCardCount = (empty($cardCount))?DEFAULT_CARD_COUNT:$cardCount;
    }else{
      $userCardCount = $cardCount+$userCard['user_card_count'];
    }
    
    
    $temp['total_card'] = $userCardCount;
    $levelUpgradeCardDetail = $cardLib->getMasterCardLevelUpgradeForCardCount($cardLevelId+1, $masterCard['card_rarity_type']);
    $temp['next_level_card_count'] = $levelUpgradeCardDetail["card_count"];
    $temp['card_rarity_type'] = $masterCard["card_rarity_type"];

    if(!empty($userCard)) {
      $cardLib->updateUserCard($userCard['user_card_id'], array("user_card_count" => $userCardCount));
    }
    $cardLevel = $cardLib->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($cardId);
    if($cardLevel['level_id']<=$cardLevelId){
      $clVal=$cardLevelId;
    }else{
      $clVal=$cardLevel['level_id'];
    }  
    
    if( empty($userCard))
    {
      $userCardId =  $cardLib->insertUserCard(array('user_id' => $userId,
                      'master_card_id' => $cardId,
                      'is_deck' => CONTENT_INACTIVE,
                      'level_id' => $clVal,
                      'user_card_count' => empty($userCardCount)?DEFAULT_CARD_COUNT:$userCardCount,
                      'created_at' => date('Y-m-d H:i:s'),
                      'status' => CONTENT_ACTIVE));

     foreach($masterCardPropertyList as $cardProperty)
     {
       //$cardPropertyValue = $cardLib->getCardPropertyValue($cardId, DEFAULT_CARD_LEVEL_ID, $cardProperty['card_property_id']);
       $cardPropertyValue = $cardLib->getCardPropertyValue($cardId, $clVal, $cardProperty['card_property_id']);
      if(empty($cardPropertyValue)){
        $cardPropertyValue = $cardLib->getCardPropertyValue($cardId, $cardLevelId, $cardProperty['card_property_id']); 
      }elseif(empty($cardPropertyValue)){
        $cardPropertyValue = $cardLib->getCardPropertyValue($cardId, DEFAULT_CARD_LEVEL_ID, $cardProperty['card_property_id']);
      }
     // print_log($cardId);
      //print_log($clVal);
      //print_log($cardProperty['card_property_id']);
       $cardLib->insertUserCardProperty(array('user_id' => $userId,
                        'card_property_id' => $cardProperty['card_property_id'],
                        'user_card_id' => $userCardId,
                        'user_card_property_value' => $cardPropertyValue['card_property_value'],
                        'created_at' => date('Y-m-d H:i:s')
                      ));
     }
    }

    return $temp;
  }

  public function rewardCopperCube($userId, $masterStadiumId )
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');

    $userLib->insertUserReward(array(
                'user_id' =>$userId,
                'cube_id' => CUBE_DYNAMITE,
                'master_stadium_id' => $masterStadiumId,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => CUBE_CAN_BE_CLAIMED));

    $userLib->updateUser($userId, array('is_copper_cube_notification_sent' => CONTENT_INACTIVE));
  }

  public function getMasterMatchStatusRewardForStadium($winStatus, $masterStadiumId, $options=array())
  {
    $sql = "SELECT *
            FROM master_match_status_reward
            WHERE win_status = :winStatus AND master_stadium_id = :masterStadiumId";

    $result = database::doSelectOne($sql, array('winStatus' => $winStatus, 'masterStadiumId' => $masterStadiumId));
    return $result;
  }
  public function getMasterMatchStatusRewardForStadiumByTower($winStatus,$circlet, $masterStadiumId, $options=array())
  {
   /* $sql = "SELECT *
            FROM master_match_status_reward
            WHERE win_status = :winStatus AND master_stadium_id = :masterStadiumId";*/
    $sql = "SELECT mmsr.master_match_status_reward_id, mmsr.master_stadium_id,mmsr.win_status, mmsr.xp, mmsr.gold, mmsrt.destroyed_towers, mmsrt.relics, mmsr.created_at
            FROM master_match_status_reward mmsr
            INNER JOIN master_match_status_reward_by_tower mmsrt ON mmsr.win_status=mmsrt.win_status
            WHERE mmsrt.win_status = :winStatus AND mmsr.master_stadium_id = :masterStadiumId AND mmsrt.destroyed_towers=:circlet";

    $result = database::doSelectOne($sql, array('winStatus' => $winStatus, 'masterStadiumId' => $masterStadiumId, 'circlet'=>$circlet));
    return $result;
  }
  public function getMaxStadiumIdMasterMatchStatusRewardForStadium($options=array())
  {
    $sql = "SELECT master_stadium_id 
            FROM master_match_status_reward 
            WHERE master_stadium_id in (SELECT MAX(master_stadium_id) 
                                        FROM master_match_status_reward) 
            GROUP BY master_stadium_id";
    /*"SELECT *
            FROM master_match_status_reward
            WHERE win_status = :winStatus AND master_stadium_id = :masterStadiumId";
*/
    $result = database::doSelectOne($sql);
    return $result;
  }

  public function getRandomCard($cubeRewardDetail, $masteStadiumId)
  {
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $cardIdList = $rareCardList = $ultraRareCardList = $cardList = array();
    $cardCount = 0;
    $excludeCardId = 0;
    $cardListValues = null;
    $cardCountV = 0;
    if($cubeRewardDetail['common'] > 0)
    {
        $probability = rand(1,100);
        $minSplitValueCommon = $cubeRewardDetail['common_card_count'] > 24 ? 24:$cubeRewardDetail['common_card_count'];
        $splitCommonValues = $rewardLib->getSplitAfterCalculationValues($cubeRewardDetail['common_card_count'], $cubeRewardDetail['common'], $minSplitValueCommon);
        $commonCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarity($masteStadiumId, CARD_RARITY_COMMON, $probability, $excludeCardId, $cubeRewardDetail['common']);
        if(count($commonCardList) < $cubeRewardDetail['common'])
        {
          if(!empty($commonCardList))
            {
              foreach($commonCardList as $item) {
                $cardIdList[$cardCount++] = $item['master_card_id'];
              }
              //$excludeCardId = implode(',',$cardIdList);
            }      
            $probability = 100;
            $commonCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarity($masteStadiumId, CARD_RARITY_COMMON, $probability, $excludeCardId, $cubeRewardDetail['common']);
            if(!empty($commonCardList))
            {
              foreach($commonCardList as $commonCard) {
                $cardIdList[$cardCount++] = $commonCard['master_card_id'];
              }
            }
        }else{
            foreach($commonCardList as $commonCard) {
              $cardIdList[$cardCount++] = $commonCard['master_card_id'];
            }
        }

        $excludeCV[] = $splitCommonValues;
        //implode(',', $splitCommonValues);
        //$cardListValues = $rewardLib->combine_arr($cardIdList, $splitCommonValues);
        $excludeCardId = implode(',',$cardIdList);
          
    }
    if($cubeRewardDetail['rare'] > 0)
    {
      $probability = rand(1,100);
      $minSplitValueRare = $cubeRewardDetail['rare_card_count'] > 24 ? 24:$cubeRewardDetail['rare_card_count'];
      $splitRareValues = $rewardLib->getSplitAfterCalculationValues($cubeRewardDetail['rare_card_count'], $cubeRewardDetail['rare'], $minSplitValueRare);
      $rareCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarity($masteStadiumId, CARD_RARITY_RARE, $probability, $excludeCardId, $cubeRewardDetail['rare']);

      if(count($rareCardList) < $cubeRewardDetail['rare'])
      {
        if(!empty($rareCardList))
        {
          foreach($rareCardList as $item) {
            $cardIdList[$cardCount++] = $item['master_card_id'];
          }
          $excludeCardId = implode(',',$cardIdList);
        }
        $probability = 100;
        $rareCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarity($masteStadiumId, CARD_RARITY_RARE, $probability, $excludeCardId, $cubeRewardDetail['rare']);
        if(!empty($rareCardList))
        {
          foreach($rareCardList as $rareCard) {
            $cardIdList[$cardCount++] = $rareCard['master_card_id'];
          }
        }
      } else
      {
        foreach($rareCardList as $rareCard) {
          $cardIdList[$cardCount++] = $rareCard['master_card_id'];
        }
      }
      
      $excludeCV[] = $splitRareValues;
      //$excludeCV = implode(',', $splitRareValues);
      $excludeCardId = implode(',',$cardIdList);

    }

    if($cubeRewardDetail['epic'] > 0)
    {
      $probability = rand(1,100);
      $minSplitValueEpic = $cubeRewardDetail['epic_card_count'] > 24 ? 24:$cubeRewardDetail['epic_card_count'];
      $splitEpicValues = $rewardLib->getSplitAfterCalculationValues($cubeRewardDetail['epic_card_count'], $cubeRewardDetail['epic'], $minSplitValueEpic);
      $epicCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarity($masteStadiumId, CARD_RARITY_EPIC, $probability, $excludeCardId, $cubeRewardDetail['epic']);
      if(count($epicCardList) < $cubeRewardDetail['epic'])
      {
        if(!empty($epicCardList))
        {
          foreach($epicCardList as $item) {
            $cardIdList[$cardCount++] = $item['master_card_id'];
          }
          //$excludeCardId = implode(',',$cardIdList);
        }

        $probability = 100;
        $epicCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarity($masteStadiumId, CARD_RARITY_EPIC, $probability, $excludeCardId, $cubeRewardDetail['epic']);

        foreach($epicCardList as $epicCard) {
          $cardIdList[$cardCount++] = $epicCard['master_card_id'];
        }
      } else
      {
        foreach($epicCardList as $epicCard) {
          $cardIdList[$cardCount++] = $epicCard['master_card_id'];
        }
      }
        $excludeCV[] = $splitEpicValues;
        //$excludeCV = implode(',', $splitEpicValues);
        $excludeCardId = implode(',',$cardIdList);
    }

    if($cubeRewardDetail['ultra_epic'] > 0)
    {
      $probability = rand(1,100);
      $minSplitValueUltraEpic = $cubeRewardDetail['ultra_epic_card_count'] > 24 ? 24:$cubeRewardDetail['ultra_epic_card_count'];
      $splitUltraEpicValues = $rewardLib->getSplitAfterCalculationValues($cubeRewardDetail['ultra_epic_card_count'], $cubeRewardDetail['ultra_epic'], $minSplitValueUltraEpic);
      $ultraEpicCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarity($masteStadiumId, CARD_RARITY_ULTRA_EPIC, $probability, $excludeCardId, $cubeRewardDetail['ultra_epic']);
      if(count($ultraEpicCardList) < $cubeRewardDetail['ultra_epic'])
      {
        if(!empty($ultraEpicCardList))
        {
          foreach($ultraEpicCardList as $item) {
            $cardIdList[$cardCount++] = $item['master_card_id'];
          }
          //$excludeCardId = implode(',',$cardIdList);
        }

        $probability = 100;
        $ultraEpicCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarity($masteStadiumId, CARD_RARITY_ULTRA_EPIC, $probability, $excludeCardId, $cubeRewardDetail['ultra_epic']);

        foreach($ultraEpicCardList as $ultraEpicCard) {
          $cardIdList[$cardCount++] = $ultraEpicCard['master_card_id'];
        }
      } else
      {
        foreach($ultraEpicCardList as $ultraEpicCard) {
          $cardIdList[$cardCount++] = $ultraEpicCard['master_card_id'];
        }
      }
     
        $excludeCV[] = $splitUltraEpicValues;
        //$excludeCV = implode(',', $splitUltraEpicValues);
      $excludeCardId = implode(',',$cardIdList);
    }

    //return $cardIdList;
    //$a = $rewardLib->array_flatten($excludeCV);
    $newArray = array();
    //$nArray = array();
    for ($i=0; $i < $cubeRewardDetail['card_count']; $i++) { 
      foreach($excludeCV[$i] as $ecv){
        $newArray[] = $ecv;         
      }
      //array_push($nArray, $newArray[$i]);
    }
    $cardListValues = $rewardLib->combine_arr($cardIdList, $newArray);
    return $cardListValues; 
  }

  public function getRandomCardWithVersion($cubeRewardDetail, $masteStadiumId, $andVer, $iosVer)
  {
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $rewardLib = autoload::loadLibrary('queryLib', 'reward');
    $cardIdList = $rareCardList = $ultraRareCardList = $cardList = array();
    $cardCount = 0;
    $excludeCardId = 0;
    $cardListValues = null;
    $cardCountV = 0;
    if($cubeRewardDetail['common'] > 0)
    {
        $probability = rand(1,100);
        $minSplitValueCommon = $cubeRewardDetail['common_card_count'] > 24 ? 24:$cubeRewardDetail['common_card_count'];
        $splitCommonValues = $rewardLib->getSplitAfterCalculationValues($cubeRewardDetail['common_card_count'], $cubeRewardDetail['common'], $minSplitValueCommon);
        $commonCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarityWithVersion($masteStadiumId, CARD_RARITY_COMMON, $probability, $excludeCardId, $cubeRewardDetail['common'],$andVer, $iosVer);
        if(count($commonCardList) < $cubeRewardDetail['common'])
        {
          if(!empty($commonCardList))
            {
              foreach($commonCardList as $item) {
                $cardIdList[$cardCount++] = $item['master_card_id'];
              }
              //$excludeCardId = implode(',',$cardIdList);
            }      
            $probability = 100;
            $commonCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarityWithVersion($masteStadiumId, CARD_RARITY_COMMON, $probability, $excludeCardId, $cubeRewardDetail['common'],$andVer, $iosVer);
            if(!empty($commonCardList))
            {
              foreach($commonCardList as $commonCard) {
                $cardIdList[$cardCount++] = $commonCard['master_card_id'];
              }
            }
        }else{
            foreach($commonCardList as $commonCard) {
              $cardIdList[$cardCount++] = $commonCard['master_card_id'];
            }
        }

        $excludeCV[] = $splitCommonValues;
        //implode(',', $splitCommonValues);
        //$cardListValues = $rewardLib->combine_arr($cardIdList, $splitCommonValues);
        $excludeCardId = implode(',',$cardIdList);
          
    }
    if($cubeRewardDetail['rare'] > 0)
    {
      $probability = rand(1,100);
      $minSplitValueRare = $cubeRewardDetail['rare_card_count'] > 24 ? 24:$cubeRewardDetail['rare_card_count'];
      $splitRareValues = $rewardLib->getSplitAfterCalculationValues($cubeRewardDetail['rare_card_count'], $cubeRewardDetail['rare'], $minSplitValueRare);
      $rareCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarityWithVersion($masteStadiumId, CARD_RARITY_RARE, $probability, $excludeCardId, $cubeRewardDetail['rare'],$andVer, $iosVer);

      if(count($rareCardList) < $cubeRewardDetail['rare'])
      {
        if(!empty($rareCardList))
        {
          foreach($rareCardList as $item) {
            $cardIdList[$cardCount++] = $item['master_card_id'];
          }
          $excludeCardId = implode(',',$cardIdList);
        }
        $probability = 100;
        $rareCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarityWithVersion($masteStadiumId, CARD_RARITY_RARE, $probability, $excludeCardId, $cubeRewardDetail['rare'],$andVer, $iosVer);
        if(!empty($rareCardList))
        {
          foreach($rareCardList as $rareCard) {
            $cardIdList[$cardCount++] = $rareCard['master_card_id'];
          }
        }
      } else
      {
        foreach($rareCardList as $rareCard) {
          $cardIdList[$cardCount++] = $rareCard['master_card_id'];
        }
      }
      
      $excludeCV[] = $splitRareValues;
      //$excludeCV = implode(',', $splitRareValues);
      $excludeCardId = implode(',',$cardIdList);

    }

    if($cubeRewardDetail['epic'] > 0)
    {
      $probability = rand(1,100);
      $minSplitValueEpic = $cubeRewardDetail['epic_card_count'] > 24 ? 24:$cubeRewardDetail['epic_card_count'];
      $splitEpicValues = $rewardLib->getSplitAfterCalculationValues($cubeRewardDetail['epic_card_count'], $cubeRewardDetail['epic'], $minSplitValueEpic);
      $epicCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarityWithVersion($masteStadiumId, CARD_RARITY_EPIC, $probability, $excludeCardId, $cubeRewardDetail['epic'],$andVer, $iosVer);
      if(count($epicCardList) < $cubeRewardDetail['epic'])
      {
        if(!empty($epicCardList))
        {
          foreach($epicCardList as $item) {
            $cardIdList[$cardCount++] = $item['master_card_id'];
          }
          //$excludeCardId = implode(',',$cardIdList);
        }

        $probability = 100;
        $epicCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarityWithVersion($masteStadiumId, CARD_RARITY_EPIC, $probability, $excludeCardId, $cubeRewardDetail['epic'],$andVer, $iosVer);

        foreach($epicCardList as $epicCard) {
          $cardIdList[$cardCount++] = $epicCard['master_card_id'];
        }
      } else
      {
        foreach($epicCardList as $epicCard) {
          $cardIdList[$cardCount++] = $epicCard['master_card_id'];
        }
      }
        $excludeCV[] = $splitEpicValues;
        //$excludeCV = implode(',', $splitEpicValues);
        $excludeCardId = implode(',',$cardIdList);
    }

    if($cubeRewardDetail['ultra_epic'] > 0)
    {
      $probability = rand(1,100);
      $minSplitValueUltraEpic = $cubeRewardDetail['ultra_epic_card_count'] > 24 ? 24:$cubeRewardDetail['ultra_epic_card_count'];
      $splitUltraEpicValues = $rewardLib->getSplitAfterCalculationValues($cubeRewardDetail['ultra_epic_card_count'], $cubeRewardDetail['ultra_epic'], $minSplitValueUltraEpic);
      $ultraEpicCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarityWithVersion($masteStadiumId, CARD_RARITY_ULTRA_EPIC, $probability, $excludeCardId, $cubeRewardDetail['ultra_epic'],$andVer, $iosVer);
      if(count($ultraEpicCardList) < $cubeRewardDetail['ultra_epic'])
      {
        if(!empty($ultraEpicCardList))
        {
          foreach($ultraEpicCardList as $item) {
            $cardIdList[$cardCount++] = $item['master_card_id'];
          }
          //$excludeCardId = implode(',',$cardIdList);
        }

        $probability = 100;
        $ultraEpicCardList = $cardLib->getMasterCardListBasedOnStadiumAndRarityWithVersion($masteStadiumId, CARD_RARITY_ULTRA_EPIC, $probability, $excludeCardId, $cubeRewardDetail['ultra_epic'],$andVer, $iosVer);

        foreach($ultraEpicCardList as $ultraEpicCard) {
          $cardIdList[$cardCount++] = $ultraEpicCard['master_card_id'];
        }
      } else
      {
        foreach($ultraEpicCardList as $ultraEpicCard) {
          $cardIdList[$cardCount++] = $ultraEpicCard['master_card_id'];
        }
      }
     
        $excludeCV[] = $splitUltraEpicValues;
        //$excludeCV = implode(',', $splitUltraEpicValues);
      $excludeCardId = implode(',',$cardIdList);
    }

    //return $cardIdList;
    //$a = $rewardLib->array_flatten($excludeCV);
    $newArray = array();
    //$nArray = array();
    for ($i=0; $i < $cubeRewardDetail['card_count']; $i++) { 
      foreach($excludeCV[$i] as $ecv){
        $newArray[] = $ecv;         
      }
      //array_push($nArray, $newArray[$i]);
    }
    $cardListValues = $rewardLib->combine_arr($cardIdList, $newArray);
    return $cardListValues; 
  }
  public function getUserRewardListForDate($userId, $cubeId, $options=array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE  user_id = :userId AND cube_id = :cubeId AND status = :status";

    $result = database::doSelect($sql, array('userId' => $userId, 'cubeId' => $cubeId, 'status' => CONTENT_CLOSED));
    return $result;
  }

  public function getLastFreeCubeRewardDetailForUser($userId, $cubeId, $options=array())
  {
    $sql = "SELECT created_at, status, claimed_at
            FROM user_reward
            WHERE user_id = :userId AND cube_id IN (".$cubeId.")
            ORDER BY created_at DESC";

    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result;
  }

}
