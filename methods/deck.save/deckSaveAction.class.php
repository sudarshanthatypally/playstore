<?php
/**
 * Author : Abhijth Shetty
 * Date   : 29-05-2019
 * Desc   : This is a controller file for deckSave Action 
 */
class deckSaveAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=deck.save", tags={"Deck"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="deck_data", name="deck_data", description="The deck_data specific to this event",
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
    print_log("--------------------deck.save---------------------");
    $deckLib = autoload::loadLibrary('queryLib', 'deck');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $result = new arrayObject();
    
    $deckData = json_decode($this->deckData,true);
    if(sizeof($deckData['deck_details']) < DECK_COUNT) {
      $this->setResponse('INSUFFICIENT_DECK_COUNT');
      return $result;
    }

    if(sizeof($deckData['deck_details']) > DECK_COUNT) {
      $this->setResponse('MAX_DECK_COUNT_REACHED');
      array_splice($deckData, 4);
    } else {
      $this->setResponse('SUCCESS');
    }

    $userDeck = $deckLib->getUserDeckDetail($this->userId);
    //print_log("--------------------Deck---------------------");
    //print_log($deckData);
    //print_log("--------------------End Deck---------------------");
    if(empty($userDeck)) {
      $deckLib->insertUserDeck(array(
        'user_id' => $this->userId,
        'deck_data' => $this->deckData,
        'created_at' => date('Y-m-d H:i:s'),
        'status' => CONTENT_ACTIVE
      ));
    } else {
      $deckLib->updateUserDeck($userDeck['user_deck_id'], array('deck_data' => json_encode($deckData, true)));
    }

    $cardList = $cardLib->getUserCardListForUserId($this->userId);
    $deckCards = $deckData['deck_details']; 
    $deckCurrDeck = $deckData['current_deck_number'];
   //print_log("--------------------Deck---------------------");
    //print_log($deckCurrDeck);
    //print_log("--------------------Deck End---------------------");
   /* 
    print_log("--------------------Card---------------------");
      print_log($cardList);
      print_log("--------------------Card End---------------------");
*/
    //change is_deck status based on card saved to deck
    foreach($cardList as $card) {
      if(in_array($card['master_card_id'], array_values(array_column($deckCards[$deckCurrDeck]['cards'], 'master_id')))) {
        ($card['is_deck'] != CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE)) : false;
      } else {
        ($card['is_deck'] == CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE)) : false; 
      }
      /*if(in_array($card['master_card_id'], array_values(array_column($deckCards[0]['cards'], 'master_id')))) {
        ($card['is_deck'] != CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE)) : false;
      } else if(in_array($card['master_card_id'], array_values(array_column($deckCards[1]['cards'], 'master_id')))) {
        ($card['is_deck'] != CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE)) : false;
      } else if(in_array($card['master_card_id'], array_values(array_column($deckCards[2]['cards'], 'master_id')))) {
        ($card['is_deck'] != CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE)) : false;
      } else if(in_array($card['master_card_id'], array_values(array_column($deckCards[3]['cards'], 'master_id')))) {
        ($card['is_deck'] != CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE)) : false;
      } else {
        ($card['is_deck'] == CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE)) : false; 
      }*/ 
    }
      //change is_deck status based on card saved to deck
    /*foreach($cardList as $card) { 
      if($deckCurrDeck==0){
        if(in_array($card['master_card_id'], array_values(array_column($deckCards[0]['cards'], 'master_id')))) {
          if($card['is_deck'] != CONTENT_ACTIVE){
            $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
          } 
          //$cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
        }else{
          if($card['is_deck'] == CONTENT_ACTIVE){
            $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE));
          }
        }
      }
      if($deckCurrDeck==1){
        if(in_array($card['master_card_id'], array_values(array_column($deckCards[1]['cards'], 'master_id')))) {
          if($card['is_deck'] != CONTENT_ACTIVE){
            $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
          } 
          //$cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
        }else{
          if($card['is_deck'] == CONTENT_ACTIVE){
            $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE));
          }
        }
      }
     if($deckCurrDeck==2){
        if(in_array($card['master_card_id'], array_values(array_column($deckCards[2]['cards'], 'master_id')))) {
          if($card['is_deck'] != CONTENT_ACTIVE){
            $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
          } 
          //$cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
        }else{
          if($card['is_deck'] == CONTENT_ACTIVE){
            $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE));
          }
        }
      }
      if($deckCurrDeck==3){
        if(in_array($card['master_card_id'], array_values(array_column($deckCards[3]['cards'], 'master_id')))) {
          if($card['is_deck'] != CONTENT_ACTIVE){
            $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
          } 
          //$cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
        }else{
          if($card['is_deck'] == CONTENT_ACTIVE){
            $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_ACTIVE));
          }
        }
      }
      /*if($deckCurrDeck) {
        print_log("-------------NOT OK---------");  
        print_log($card['master_card_id']);
        print_log("-------------NOT OK END---------");
        ($card['is_deck'] == CONTENT_ACTIVE) ? $cardLib->updateUserCard($card['user_card_id'], array("is_deck" => CONTENT_INACTIVE)) : false; 
      }
    }*/

    return $result;
  }  
}