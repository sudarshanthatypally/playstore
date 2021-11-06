<?php

  require_once(dirname(__FILE__)."/../base/autoload.class.php");
  autoload::init(dirname(__FILE__)."/../");

  $userLib = autoload::loadLibrary('queryLib', 'user');
  $rewardLib = autoload::loadLibrary('queryLib', 'reward');
  $pushNotificationLib = autoload::loadLibrary('utilityLib', 'pushNotification');

  //checks unlocking time of the rewarded cubes.
  $rewardDetail = $userLib->getProcessingUserReward();//getUserReward

  foreach ($rewardDetail as $reward)
  {
    $user = $userLib->getUserDetail($reward['user_id']);
    /*$maxTime = ($reward['cube_id'] == CUBE_FIRECRACKER)?UNLOCK_CUBE_FIRECRACKER_TIMEOUT:(($reward['cube_id'] == CUBE_BOMB)?UNLOCK_CUBE_BOMB_TIMEOUT:UNLOCK_CUBE_ROCKET_TIMEOUT);
    */
    switch ($reward['cube_id']) {
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
    }
    $maxTime = $mTime;
   //$maxTime = ($reward['cube_id'] == CUBE_TITANIUM)?UNLOCK_CUBE_TITANIUM_TIMEOUT:(($reward['cube_id'] == CUBE_DIAMOND)?UNLOCK_CUBE_DIAMOND_TIMEOUT:UNLOCK_CUBE_PLATINUM_TIMEOUT);
 
    if(($reward['claimed_at']+$maxTime - time()) <= 0)
    {
      //$cubeName = ($reward['cube_id'] == CUBE_TITANIUM)?"Titanium":(($reward['cube_id'] == CUBE_DIAMOND)?"Diamond":"Platinum");
      /*$cubeName = ($reward['cube_id'] == CUBE_FIRECRACKER)?"Fire Cracker":(($reward['cube_id'] == CUBE_BOMB)?"Bomb":"Rocket");*/
      switch ($reward['cube_id']) {
        case CUBE_FIRECRACKER:
         $cName = "Fire Cracker";
          break;
        case CUBE_BOMB:
          $cName = "Bomb";
          break;
        case CUBE_ROCKET:
          $cName = "Rocket";
          break;
        case CUBE_DYNAMITE:
          $cName = "Dynamite";
          break;
        case CUBE_METALBOMB:
          $cName = "Metal Bomb";
          break;
      }
      $cubeName = $cName;
      $paramList['status'] = CUBE_CAN_BE_CLAIMED;
      $userLib->updateUserReward($reward['user_reward_id'], $paramList);
      $message = "The ".$cubeName." Cube is unlocked. Open it to see what is inside";
      $notification = $pushNotificationLib->sendpushNotification($reward['user_id'],$message);
    }
  }


?>
