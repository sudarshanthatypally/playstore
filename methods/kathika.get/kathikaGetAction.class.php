<?php
/**
 * Author : Abhijth Shetty
 * Date   : 15-01-2018
 * Desc   : This is a controller file for KathikaGet Action
 */
class kathikaGetAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=kathika.get", tags={"Kathika"}, 
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
    $kathikaLib = autoload::loadLibrary('queryLib', 'kathika');
    $result = array();

    $kathikaList = $kathikaLib->getKathikaList();
    $kathikaUnlockedList = $kathikaLib->getKathikaUnlockedList($this->userId);
    if(count($kathikaUnlockedList) != 0){
      foreach ($kathikaList as $kathika)
      {
        $temp = $kathika;
        foreach ($kathikaUnlockedList as $kuList) {
          if($kathika['kathika_id'] ==$kuList['kathika_id']){
            $temp['kathika_id'] = $kuList['kathika_id'];
            $temp['chaptername'] = $kuList['chaptername'];
            $temp['url_link'] = $kuList['url_link'];
            $temp['img_link'] = $kuList['img_link'];
            $temp['status'] = $kuList['status'];
            $temp['required_crystal_amount'] = $kuList['required_crystal_amount'];  
          }
        }
        $result[] = $temp; 
      }
    }else{
      foreach ($kathikaList as $kathika)
      {
        $temp = array();
        $temp['kathika_id'] = $kathika['kathika_id'];
        $temp['chaptername'] = $kathika['chaptername'];
        $temp['url_link'] = $kathika['url_link'];
        $temp['img_link'] = $kathika['img_link'];
        $temp['status'] = $kathika['status'];
        $temp['required_crystal_amount'] = $kathika['required_crystal_amount'];

        $result[] = $temp;
      }
    }
    //$result[] = $kathikaUnlockedList;
    $this->setResponse('SUCCESS');
    return array('kathika_list' => $result);
  }
}
