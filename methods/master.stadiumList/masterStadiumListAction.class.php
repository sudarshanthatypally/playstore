<?php
/**
 * Author : Sudarshan Thatypally
 * Date   : 22-07-2021
 * Desc   : This is a controller file for masterStadiumList Action
 */



class masterStadiumListAction extends baseAction{

    /**
   * @OA\Get(path="?methodName=master.stadiumList", tags={"Stadium"}, 
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
    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $result = array();

    $masterStadiumList = $masterLib->getMasterStadiumList();
    $i=1;
    foreach($masterStadiumList as $item)
    {
      if($i<=10){
        $temp = array();
        $temp['stadium_id'] = $item['master_stadium_id'];
        $temp['stadiumname'] = $item['title'];
        $temp['gamepley_img_link'] = $item['gamepley_img_link'];
        $temp['mainmenu_img_link'] = $item['mainmenu_img_link'];
        $temp['stadiumname_img_link'] = $item['stadiumname_img_link'];
        $temp['miniarena_img_link'] = $item['miniarena_img_link'];
        $temp['min_relic'] = $item['relics_count_min'];
        $temp['max_relic'] = $item['relics_count_max'];
        $temp['status'] = $item['status'];
        $result[] = $temp;
      }
      $i++;
    }

    $this->setResponse('SUCCESS');
    return array('stadiumimage_list' => $result);
  }
}
