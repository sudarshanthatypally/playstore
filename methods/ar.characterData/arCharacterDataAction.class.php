<?php
/**
 * Author : Abhijth Shetty
 * Date   : 15-01-2018
 * Desc   : This is a controller file for KathikaGet Action
 */
class arCharacterDataAction extends baseAction{
   /**
   * @OA\Get(path="?methodName=ar.characterData", tags={"AR Planet"}, 
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
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $result = array();
    $planetList = $cardLib->getPlanetsList($this->userId);
    $charactersList = $cardLib->getCharactersList($this->userId);
    $charactersUnlockedList = $cardLib->getCharacterUnlockedList($this->userId);
   // $planet_result = array();
    foreach ($planetList as $planets)
    {
     // $temp = array();
      $planet_result = array();
      $char_results = array();
      $planet_result['planetID'] = $planets['planet_no'];
      $planet_result['planetname'] = $planets['planet_name'];
      $planet_result['planetdiscription'] = $planets['planet_desc'];

      if(count($charactersUnlockedList) != 0){
        foreach ($charactersList as $characters)
        {
          if($characters['planet_no']==$planets['planet_no']){
            $temp = $characters;
            foreach ($charactersUnlockedList as $cuList) {
              if($characters['card_id'] ==$cuList['card_id']){
                $temp['characterID'] = $cuList['card_id'];
                $temp['character_name'] = $cuList['character_name'];
                $temp['description'] = $cuList['description'];
                $temp['buy_type'] = $cuList['buy_type']; 
                $temp['amount'] = $cuList['amount'];  
                $temp['materialid'] = $cuList['meterial_id'];
                $temp['position'] = $cuList['position'];  
                $temp['rotation'] = $cuList['rotation']; 
                $temp['unlock_level'] = $cuList['unlock_level'];
                $temp['bundle_status'] = $cuList['bundle_status'];
                $temp['status'] = $cuList['status']; 
                $temp['planet_id'] = $cuList['planet_no']; 
              }
            }
            $char_results[] = $temp;  
          }
        }        
      }else{
        foreach ($charactersList as $characters) 
        {
          if($characters['planet_no']==$planets['planet_no']){
            $temp = array();
            $temp['characterID'] = $characters['card_id'];
            $temp['character_name'] = $characters['character_name'];
            $temp['description'] = $characters['description'];
            $temp['buy_type'] = $characters['buy_type']; 
            $temp['amount'] = $characters['amount']; 
            $temp['materialid'] = $characters['meterial_id'];
            $temp['position'] = $characters['position'];  
            $temp['rotation'] = $characters['rotation']; 
            $temp['status'] = $characters['status']; 
            $temp['unlock_level'] = $characters['unlock_level'];
            $temp['bundle_status'] = $characters['bundle_status'];
            
            $temp['planet_id'] = $characters['planet_no'];
            $char_results[] = $temp; 
          }   
        }
      }
      $planet_result['listofcharacters']=$char_results;  
      $result[]=$planet_result;
   }
   
   

    //$result[] = $kathikaUnlockedList;
    $this->setResponse('SUCCESS');
    return array('userid'=>$this->userId, 'listofplanets' => $result);
  }
}
