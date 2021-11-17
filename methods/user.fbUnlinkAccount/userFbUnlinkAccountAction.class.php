<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 12-12-2020
 * Desc   : This is a controller file for userFbUnlinkAccount Action
 */
class userFbUnlinkAccountAction extends baseAction{
	 /**
   * @OA\Get(path="?methodName=user.fbUnlinkAccount", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
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
    $facebookLib = autoload::loadLibrary('utilityLib', 'facebook');
    $userLib = autoload::loadLibrary('queryLib', 'user');

   header('Content-Type: application/json');
    if(!empty($_GET['id'])){
      $this->setResponse('SUCCESS');
      return array('fb_data' => "data deleted");
    }
    $signed_request = $_POST['signed_request'];
    //$acc = $facebookLib->parse_signed_request();
    $data = $facebookLib->parse_signed_request($signed_request);

    $user_id = $data['user_id'];
    /*$status_url = $user_id; // URL to track the deletion
    $confirmation_code = $user_id; // unique code for the deletion request
    $data = array(
      'url' => $status_url,
      'confirmation_code' => $confirmation_code
    );*/

    $paramList = array();
    $paramList['facebook_id'] = "";
    $userLib->updateFbUser($user_id, $paramList);
    
    header('location: https://epikoregal.com/EPIKO/staging/fb/getid.php?u_id='.$user_id);
   //echo json_encode($data).",";
 
    //$this->setResponse('SUCCESS');
    //return array('fb_data' => $data);
    //parse_signed_request
    //$signed_request
    /*
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    $result = array();

    $masterAchievementList = $achievementLib->getMasterAchievementList();
    foreach($masterAchievementList as $item)
    {
      $temp = array();
      $temp['master_achievement_id'] = $item['master_achievement_id'];
      $temp['title'] = $item['title'];
      $temp['description'] = $item['description'];
      $temp['xp'] = $item['xp'];
      $userAchievement = $achievementLib->getUserAchievementListForAchievementId($this->userId, $item['master_achievement_id']);
      $temp['is_achieved'] = empty($userAchievement)?"False":"True";
      $result[] = $temp;
    }

    $this->setResponse('SUCCESS');
    return array('achievement_list' => $result);*/
  }
}
