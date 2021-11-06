<?php
/**
 * Author : Abhijth Shetty
 * Date   : 16-01-2018
 * Desc   : This is a controller file for notificationGet Action
 */
class kingdomNotificationAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=kingdom.notification", tags={"Kingdom"}, 
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
    $notificationLib = autoload::loadLibrary('queryLib', 'notification');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $result =  $unreadMessage = array();
    $result['content'] = array();

    $notificationList = $notificationLib->getNotificationKingdomListForUser($this->userId, $this->lastNotificationId, $this->limit);

    if(!empty($notificationList))
    {

      foreach($notificationList as $item)
      {
        $content = array();
        $data = json_decode($item['data'], true);
        $content['notification_id'] = $item['notification_id'];

        if($item['notification_type'] == 5)
        {
          $content['notification_type'] = 5;
          $content['data'] = $data;
          $content['kingdom_id'] = $data['kingdom_id'];
          $content['message'] = " Accepted ";
        }

        /*if($item['notification_type'] == NOTIFICATION_TYPE_CUBE_UNLOCK)
        {
          $content['notification_type'] = NOTIFICATION_TYPE_CUBE_UNLOCK;
          $content['data'] = $data;
          $content['message'] = " Cube rewarded ";
        }

        if($item['notification_type'] == NOTIFICATION_TYPE_USER_INACTIVE)
        {
          $content['notification_type'] = NOTIFICATION_TYPE_USER_INACTIVE;
          $content['data'] = $data;
          $content['message'] = "User inactive";
        }


        if($item['notification_type'] == NOTIFICATION_TYPE_INVITE_ACCEPTED)
        {
          $content['notification_type'] = NOTIFICATION_TYPE_INVITE_ACCEPTED;
          $content['data'] = $data;
          $content['is_room_active_message'] = "1. active, 2. inactive";
          $content['message'] = "Invite accepted";
        }*/

        $result['content'][] = $content;
      }

      $result['last_notification_id'] = (!empty($result['content']))?$result['content'][0]['notification_id']:$this->lastNotificationId;

      $this->setResponse('SUCCESS');
      return $result;
    }

    $result['last_notification_id'] = $this->lastNotificationId;

    $this->setResponse('SUCCESS');
    return $result;
  }
}
