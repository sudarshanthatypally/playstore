<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for cardUnlock Action
 */
class userClaimKathikaAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=user.claimKathika", tags={"Users"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="kathika_id", name="kathika_id", description="The kathika_id specific to this event",
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
    $kathikaLib = autoload::loadLibrary('queryLib', 'kathika');
    $questLib = autoload::loadLibrary('queryLib', 'quest');

    $result = array();
    $userId = $this->userId;
    $user = $userLib->getUserDetail($this->userId);
    $kathikaDetails = $kathikaLib->getkathikaDetails($this->kathikaId);
    $checkKathika = $kathikaLib->checkKathikaDetails($this->kathikaId, $this->userId);
    if($user['crystal'] < $kathikaDetails['required_crystal_amount']){
      $this->setResponse('KATHIKA_CRYSTAL_FAILED');
      return new ArrayObject();  
    }
   /* print_log("=========================== userClaimKathikaAction =================================");
    print_log("user crystal::".$user['crystal']);
    print_log("kathika crystal::".$kathikaDetails['required_crystal_amount']);
    print_log("=========================== userClaimKathikaAction END =================================");
  */
    if($checkKathika < 1 ){
      $remain_crystal = $user['crystal'] - $kathikaDetails['required_crystal_amount'];
      $userLib->updateUser($userId, array('crystal' => $remain_crystal));
     // $userLib->updateUser($this->$userId, array('crystal' => $remain_crystal));
      $data['kathika_id'] = $this->kathikaId;
      $data['user_id'] = $this->userId;
      $data['status'] = 10;
      $data['created_at'] = date('Y-m-d H:i:s');
      $kathikaPaid = $kathikaLib->unlockUserKathika($data);

      //$result[] = $userId;
      //If user gettting the card for the first time then add to user card for that user
      if(!empty($kathikaPaid) && $kathikaPaid)
      {
        $userDetail = $userLib->getUserDetail($this->userId);
        $result['user_id'] = $this->userId;
        $result['crystal'] = $userDetail['crystal'];
        $result['kathika_id'] = $this->kathikaId;
        $result['chaptername']=$kathikaDetails['chaptername'];
        $result['url_link']=$kathikaDetails['url_link'];
        $result['img_link']=$kathikaDetails['img_link'];
        $result['status']=10;
      }

      if(!$kathikaPaid)
      {
        $this->setResponse('KATHIKA_FAILED');
        return new ArrayObject();
      }
      $questLib->insertMasterQuestInventory(array(
        'quest_id' => 3,
        'time' => date('Y-m-d H:i:s'),
        'user_id' => $this->userId,
        'status' => CONTENT_ACTIVE,
        'created_at' => date('Y-m-d H:i:s')));

      $this->setResponse('SUCCESS');
      return $result;
    }else{
      if($user['crystal'] > $kathikaDetails['required_crystal_amount']){
        $this->setResponse('KATHIKA_CRYSTAL_FAILED');
        return new ArrayObject();  
      }else{
        $this->setResponse('KATHIKA_ALREADY_FAILED');
        return new ArrayObject();  
      }
      
    }
  }
}
