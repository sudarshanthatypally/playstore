<?php
/**
 * Author : Abhijth Shetty
 * Date   : 16-03-2018
 * Desc   : This is a controller file for achievementList Action
 */
 /**
 * @OA\Server(url="https://epikoregalapi.com/EPIKO/playstore/rest.php")
 * @OA\Info(title="Epiko Regal", version="1.0",
 * @OA\Contact(
 *     email="sudarshant@wharfstreetstudios.com"
 *   )
 * )
 */
class achievementTest2ListAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=achievement.list", tags={"Achievements"}, 
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
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
    return array('achievement_list' => $result);
  }
}
