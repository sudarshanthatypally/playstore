<?php

  require_once(dirname(__FILE__)."/../base/autoload.class.php");
  autoload::init(dirname(__FILE__)."/../");

  $userLib = autoload::loadLibrary('queryLib', 'user');
  $rewardLib = autoload::loadLibrary('queryLib', 'reward');
  $notificationLib = autoload::loadLibrary('queryLib', 'notification');

  //checks unlocking time of the rewarded cubes.

  $rewardDetail = $userLib->getProcessingUserReward();//getUserReward

  foreach ($rewardDetail as $reward)
  {
    $user = $userLib->getUserDetail($reward['user_id']);
    /*$maxTime = ($reward['cube_id'] == CUBE_FIRECRACKER)?UNLOCK_CUBE_FIRECRACKER_TIMEOUT:($reward['cube_id'] == CUBE_BOMB)?UNLOCK_CUBE_BOMB_TIMEOUT:UNLOCK_CUBE_ROCKET_TIMEOUT;*/
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
      default:
        break;
    }
    $maxTime = $mTime;
    //$maxTime = ($reward['cube_id'] == CUBE_TITANIUM)?UNLOCK_CUBE_TITANIUM_TIMEOUT:($reward['cube_id'] == CUBE_DIAMOND)?UNLOCK_CUBE_DIAMOND_TIMEOUT:UNLOCK_CUBE_PLATINUM_TIMEOUT;

    if(($reward['claimed_at']+$maxTime - time()) <= 0)
    {
      $paramList['status'] = CUBE_CAN_BE_CLAIMED;
      $userLib->updateUserReward($reward['user_reward_id'], $paramList);
    }
  }

  //Reward the copper cube after every 4hrs when not claimed cube less than 2.
  // $userList = $userLib->getUserList();
  //
  // foreach ($userList as $user)
  // {
  //   $canBeClaimedCopperCubeList = $userRecentCopperRewardDetail = array();
  //   $canBeClaimedCopperCubeList = $userLib->getUserRewardForCanClaimStatusBasedOnCube($user['user_id'], CUBE_COPPER);
  //
  //   $userRecentCopperRewardDetail = $rewardLib->getLastCubeRewardDetailForUser($user['user_id'], CUBE_COPPER);
  //
  //   if(!empty($userRecentCopperRewardDetail['created_at']) &&  ((($userRecentCopperRewardDetail['claimed_at']) + UNLOCK_CUBE_COPPER_TIMEOUT)- time()) <= 0 )
  //   {
  //     if(!empty($canBeClaimedCopperCubeList))
  //     {
  //       if( count($canBeClaimedCopperCubeList) < 1 )
  //       {
  //         $rewardLib->rewardCopperCube($user['user_id'],$user['master_stadium_id'] );
  //         //notification
  //         $data = array("user_id"=>$user['user_id'], "cube_id" => CUBE_COPPER);
  //         $notificationId = $notificationLib->addNotification(NOTIFICATION_TYPE_COPPER_REWARD, CONTENT_TYPE_USER, $user['user_id'], $data);
  //       }
  //     } else
  //     {
  //       $rewardLib->rewardCopperCube($user['user_id'],$user['master_stadium_id'] );
  //       //notification
  //       $data = array("user_id"=>$user['user_id'], "cube_id" => CUBE_COPPER);
  //       $notificationId = $notificationLib->addNotification(NOTIFICATION_TYPE_COPPER_REWARD, CONTENT_TYPE_USER, $user['user_id'], $data);
  //
  //     }
  //   }
  //
  //   //player is new to the game
  //   if(empty($userRecentCopperRewardDetail['created_at']))
  //   {
  //     if((strtotime($user['created_at']) + UNLOCK_CUBE_COPPER_TIMEOUT- time()) <= 0 )
  //     {
  //       $rewardLib->rewardCopperCube($user['user_id'],$user['master_stadium_id'] );
  //       //notification
  //       $data = array("user_id"=>$user['user_id'], "cube_id" => CUBE_COPPER);
  //       $notificationId = $notificationLib->addNotification(NOTIFICATION_TYPE_COPPER_REWARD, CONTENT_TYPE_USER, $user['user_id'], $data);
  //     }
  //   }
  // }

?>
