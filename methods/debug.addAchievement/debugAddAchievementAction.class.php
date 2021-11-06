<?php
/**
 * Author : Abhijth Shetty
 * Date   : 16-03-2018
 * Desc   : This is a controller file for debugAddAchievement Action
 */
class debugAddAchievementAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=debug.addAchievement", tags={"Debug"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="master_chievement_id", name="master_chievement_id", description="The master_chievement_id specific to this event",
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
    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    $result = array();

    $achievementLib->insertUserAchievement(array('user_id' => $this->userId,
                       'master_achievement_id' => $this->masterAchievementId,
                       'created_at' => date('Y-m-d H:i:s'),
                       'status' => CONTENT_ACTIVE));

    $this->setResponse('SUCCESS');
    return $result;
  }
}
