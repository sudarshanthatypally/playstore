<?php

  require_once(dirname(__FILE__)."/../base/autoload.class.php");
  autoload::init(dirname(__FILE__)."/../");

  $userLib = autoload::loadLibrary('queryLib', 'user');
  $rewardLib = autoload::loadLibrary('queryLib', 'reward');
  $pushNotificationLib = autoload::loadLibrary('utilityLib', 'pushNotification');

  $userList = $userLib->getUserList();
  foreach($userList as $user)
  {
    $user = $userLib->getUserDetail($user['user_id']);
    $userRewardList = $userLib->getUserRewardActiveListForCube($user['user_id'], CUBE_COPPER, CONTENT_CLOSED);
    //Check User eligibity for the Copper reward
    if(empty($userRewardList))
    {
      $userRecentCopperRewardDetail = $rewardLib->getLastCubeRewardDetailForUser($user['user_id'], CUBE_COPPER);

      $unlockTime = (($userRecentCopperRewardDetail['claimed_at']) + UNLOCK_CUBE_COPPER_TIMEOUT);
      $unlockTime = ($unlockTime-time() <= 0)?0:$unlockTime-time();

      $noReward =((strtotime($user['created_at']) + UNLOCK_CUBE_COPPER_TIMEOUT));
      $noReward = ($noReward-time() <= 0)?0:$noReward-time();

      $userRecentCopperRewardDetail = $rewardLib->getLastCubeRewardDetailForUser($user['user_id'], CUBE_COPPER);
      $result['reward_unlock_time'] = (empty($userRecentCopperRewardDetail))?$noReward:$unlockTime;

      if($result['reward_unlock_time'] <= 0 && $user['is_copper_cube_notification_sent'] == CONTENT_ACTIVE){
        $message = "Hey ! You can get a copper cube now. Claim it soon.";
        $notification = $pushNotificationLib->sendpushNotification($user['user_id'], $message);
        $userLib->updateUser($user['user_id'], array('is_copper_cube_notification_sent' => CONTENT_INACTIVE));
      }
    }
  }

?>
