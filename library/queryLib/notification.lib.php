<?php
class notification{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getNotificationList($options=array())
  {
    $sql = "SELECT *
            FROM notification";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getNotificationDetail($notificationId, $options=array())
  {
    $sql = "SELECT *
            FROM notification
            WHERE notification_id = :notificationId";

    $result = database::doSelectOne($sql, array('notificationId'=>$notificationId));
    return $result;
  }

  public function insertNotification($options=array())
  {
    $sql = "INSERT INTO notification ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function updateNotification($notificationId, $options=array())
  {
    $sql = "UPDATE notification SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE notification_id =:notificationId";
    $options['notificationId'] = $notificationId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function getRejectedDetailByUsers($userId, $options=array())
  {
    $sql = "SELECT *
            FROM notification
            WHERE content_id = :userId AND notification_type=12";

    $result = database::doSelect($sql, array('userId'=>$userId));
    return $result;
  }
  public function deleteNotification($notificationId, $options=array())
  {
    $sql = "DELETE FROM user
            WHERE notification_id = :notificationId";

	  $result = database::doDelete($sql, array('notificationId'=>$notificationId));
    return $result;
  }

  public function deleteNotificationByDate($options=array())
  {
    $sql = "DELETE FROM notification
            WHERE created_at < NOW() - INTERVAL 1 DAY";

	  $result = database::doDelete($sql);
    return $result;
  }
 /* public function deleteMsgByDate($options=array())
  {
    $sql = "DELETE FROM kingdom_messages
            WHERE msg_type = 7 AND created_at < NOW() - INTERVAL 1 DAY";

	  $result = database::doDelete($sql);
    return $result;
  }*/
  public function deleteMsgByPopUp($userId, $notificationId, $options=array())
  {
    $sql = "DELETE FROM notification 
            WHERE content_id=:userId AND notification_id=:notificationId AND notification_type = 12";

	  $result = database::doDelete($sql, array('userId'=>$userId, 'notificationId'=>$notificationId));
    return $result;
  }
  public function deleteKingdomNotification($userId, $options=array())
  {
    $sql = "DELETE FROM notification
            WHERE content_id = :userId AND notification_type=7";

	  $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;
  }
  public function deleteKingdomNotificationOnAccept($userId, $options=array())
  {
    $sql = "DELETE FROM notification
            WHERE content_id = :userId AND notification_type=12";

	  $result = database::doDelete($sql, array('userId'=>$userId));
    return $result;
  }
  public function addNotification($type, $contentType, $contentId, $data, $options = array())
  {
    $notificationId = $this->insertNotification(array("content_type"=>$contentType, "notification_type"=>$type, "content_id"=>$contentId, "data"=>json_encode($data), "created_at"=>date('Y-m-d H:i:s'), "status"=>CONTENT_ACTIVE));
    return $notificationId;
  }

  public function getNotificationListForUser($userId, $lastNotificationId, $limit, $options=array())
  {
    $sql = "SELECT  notification.notification_id, notification.content_type, notification.content_id, notification.notification_type, notification.data, user.notification_status
            FROM notification
            INNER JOIN user ON user.user_id = notification.content_id
            WHERE notification.content_type = ".CONTENT_TYPE_USER."
            AND notification.content_id = ".$userId." AND notification.notification_id > ".$lastNotificationId."
            AND notification.status = ".CONTENT_ACTIVE." AND user.notification_status = ".CONTENT_ACTIVE." 
            AND user.user_id = notification.content_id
            ORDER BY notification.notification_id DESC
            LIMIT 0,".$limit;

    $result = database::doSelect($sql);
    return $result;
  }
  public function getNotificationKingdomListForUser($userId, $lastNotificationId, $limit, $options=array())
  {
    $sql = "SELECT  notification.notification_id, notification.content_type, notification.content_id, notification.notification_type, notification.data, user.notification_status
            FROM notification
            INNER JOIN user ON user.user_id = notification.content_id
            WHERE notification.content_type = ".CONTENT_TYPE_USER."
            AND notification.content_id = ".$userId." AND notification.notification_id > ".$lastNotificationId."
            AND notification.status = ".CONTENT_ACTIVE." AND user.notification_status = ".CONTENT_ACTIVE." 
            AND user.user_id = notification.content_id
            ORDER BY notification.notification_id DESC
            LIMIT 0,".$limit;

    $result = database::doSelect($sql);
    return $result;
  }

  public function processInaciveUserNotification($notifingInactiveUser, $inactiveHour)
  {
    $notificationLib = autoload::loadLibrary('queryLib', 'notification');

    foreach ($notifingInactiveUser as $user)
    {
      $data = array("user_id"=>$user['user_id'], "user_inactive_hour" => $inactiveHour);
      $notificationId = $notificationLib->addNotification(NOTIFICATION_TYPE_USER_INACTIVE, CONTENT_TYPE_USER, $user['user_id'], $data);
    }
  }

}
