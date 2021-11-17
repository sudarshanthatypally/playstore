<?php
/**
 * Author : Abhijth Shetty
 * Date   : 14-05-2018
 * Desc   : This is a controller file for debugUpdateUserData Action
 */
class debugUpdateUserDataAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=debug.updateUserData", tags={"Debug"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="relics", name="relics", description="The relics specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="xp", name="xp", description="The xp specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="crystal", name="crystal", description="The crystal specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="circlet", name="circlet", description="The circlet specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="gold", name="gold", description="The gold specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="level_id", name="level_id", description="The level_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="master_stadium_id", name="master_stadium_id", description="The master_stadium_id specific to this event",
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
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $result = $result['achievement'] = new ArrayObject();

    $paramList =   array();

    $userDetail = $userLib->getUserDetail($this->userId);


    if($this->levelId != "")
    {
      $paramList['level_id'] = $this->levelId;
    }

    if($this->relics != "")
    {
      $paramList['relics'] = $this->relics;
    }

    if($this->xp != "")
    {
      $paramList['xp'] = $this->xp;
    }

    if($this->crystal != "")
    {
      $paramList['crystal'] = $this->crystal;
    }

    if($this->circlet != "")
    {
      $paramList['circlet'] = $this->circlet;
    }

    if($this->gold != "")
    {
      $paramList['gold'] = $this->gold;
    }

    if($this->xp != "")
    {
      $paramList['xp'] = $this->xp;
    }

    if($this->masterStadiumId != "")
    {
      $paramList['master_stadium_id'] = $this->masterStadiumId;
    }

    if(!empty($paramList)){
      $userLib->updateUser($this->userId, $paramList);
    }

    $this->setResponse('SUCCESS');
    return $result;
  }
}
