<?php

  // Finding inActive user based on HOUR
  require_once(dirname(__FILE__)."/../base/autoload.class.php");
  autoload::init(dirname(__FILE__)."/../");

  $userLib = autoload::loadLibrary('queryLib', 'user');
  $notificationLib = autoload::loadLibrary('queryLib', 'notification');
  $notifingOneHourInactiveUser = $notifingOneDayInactiveUser = array();

  //ONE HOUR Inactivity of User
  $notifingOneHourInactiveUser = $userLib->getInactiveUserList(INACTIVITY_ONE_HOUR_TIMEOUT);

  //24 HOUR Inactivity of User
  $notifingOneDayInactiveUser = $userLib->getInactiveUserList(INACTIVITY_ONE_DAY_TIMEOUT);

  if(!empty($notifingOneHourInactiveUser) || !empty($notifingOneDayInactiveUser))
  {
    $notificationLib->processInaciveUserNotification($notifingOneHourInactiveUser, INACTIVITY_ONE_HOUR_TIMEOUT );
    $notificationLib->processInaciveUserNotification($notifingOneDayInactiveUser, INACTIVITY_ONE_DAY_TIMEOUT );
  }

?>
