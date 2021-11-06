<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-12-2017
 * Desc   : This is a controller file for cardUnlock Action
 */
class arClaimCharacterAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=ar.claimCharacter", tags={"AR Planet"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="character_id", name="character_id", description="The character_id specific to this event",
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
    $cardLib = autoload::loadLibrary('queryLib', 'card');

    $result = array();
    $userId = $this->userId;
    $user = $userLib->getUserDetail($this->userId);
    $characterDetails = $cardLib->getCharacterDetails($this->characterId);
    $checkCharacter = $cardLib->checkCharacterDetails($this->characterId, $this->userId);
    
    print_log("=========================== arClaimCharacterAction =================================");
    print_log("user crystal::".$user['crystal']);
    print_log("character crystal amount::".$characterDetails['amount']);
    print_log("=========================== arClaimCharacterAction END =================================");
    if($user['crystal'] < $characterDetails['amount']){
      $this->setResponse('CHARACTER_CRYSTAL_FAILED');
      return new ArrayObject();  
    }
    if($checkCharacter < 1 ){
      // 0 Indicates that Buy wuth Gems/Crystals
      if($characterDetails['buy_type'] == 0){
        $remain_crystal = $user['crystal'] - $characterDetails['amount'];
        $userLib->updateUser($userId, array('crystal' => $remain_crystal));
      }
      // 1 Indicates that Buy wuth Gold
      if($characterDetails['buy_type'] == 1){
        $remain_gold = $user['gold'] - $characterDetails['amount'];
        $userLib->updateUser($userId, array('gold' => $remain_gold ));
      }
      // 2 Indicates that Buy wuth Token
      if($characterDetails['buy_type'] == 2){

      }
      

     // $userLib->updateUser($this->$userId, array('crystal' => $remain_crystal));
      $data['card_id'] = $this->characterId;
      $data['user_id'] = $this->userId;
      $data['status'] = 1;
      $data['created_at'] = date('Y-m-d H:i:s');
      $characterPaid = $cardLib->unlockArCharacter($data);

      //$result[] = $userId;
      //If user gettting the card for the first time then add to user card for that user
      if(!empty($characterPaid) && $characterPaid)
      {
        $userDetail = $userLib->getUserDetail($this->userId);
        $result['user_id'] = $this->userId;
        $result['crystal'] = $userDetail['crystal'];
        $result['gold'] = $userDetail['gold'];
        $result['characterID'] = $this->characterId;
        $result['character_name'] = $characterDetails['character_name'];
        $result['description'] = $characterDetails['description'];
        $result['buy_type'] = $characterDetails['buy_type']; 
        $result['amount'] = $characterDetails['amount']; 
        $result['materialid'] = $characterDetails['meterial_id'];
        $result['position'] = $characterDetails['position'];  
        $result['rotation'] = $characterDetails['rotation']; 
        $result['unlock_level'] = $characterDetails['unlock_level'];
        $result['bundle_status'] = $characterDetails['bundle_status'];
        $result['status'] = 1;  
        $result['planet_id'] = $characterDetails['planet_no'];
        
      }

      if(!$characterPaid)
      {
        $this->setResponse('CHARACTER_FAILED');
        return new ArrayObject();
      }

      $this->setResponse('SUCCESS');
      return $result;
    }else{
      if($characterDetails['buy_type']==0){
        if($user['crystal'] < $characterDetails['amount']){
          $this->setResponse('CHARACTER_CRYSTAL_FAILED');
          return new ArrayObject();  
        }else{
          $this->setResponse('CHARACTER_ALREADY_FAILED');
          return new ArrayObject();  
        }
      }
      elseif($characterDetails['buy_type']==1){
        if($user['gold'] < $characterDetails['amount']){
          $this->setResponse('CHARACTER_GOLD_FAILED');
          return new ArrayObject();  
        }
        else{
          $this->setResponse('CHARACTER_ALREADY_FAILED');
          return new ArrayObject();  
        }
      }else{
        $this->setResponse('CHARACTER_ALREADY_FAILED');
        return new ArrayObject();  
      }
      /*elseif($characterDetails['buy_type']==2){
        if($user['gold'] > $characterDetails['amount']){
          $this->setResponse('CHARACTER_CRYSTAL_FAILED');
          return new ArrayObject();  
        }
        else{
          $this->setResponse('CHARACTER_ALREADY_FAILED');
          return new ArrayObject();  
        }
      }*/
      
    }
  }
}