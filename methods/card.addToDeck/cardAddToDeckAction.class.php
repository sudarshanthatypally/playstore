<?php
/**
 * Author : Abhijth Shetty
 * Date   : 03-01-2018
 * Desc   : This is a controller file for cardAddToDeck Action
 */
class cardAddToDeckAction extends baseAction{
	/**
   * @OA\Get(path="?methodName=card.addToDeck", tags={"Cards"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="user_id", name="user_id", description="The user ID specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="access_token", name="access_token", description="The access_token specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="master_card_id", name="master_card_id", description="The master_card_id specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Parameter(parameter="replace_with_card", name="replace_with_card", description="The replace_with_card specific to this event",
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
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $result = array();

    $deckList = $cardLib->getUserCardForActiveDeck($this->userId, DECK_ACTIVE);
    $cardDetail = $cardLib->getUserCardDetailForMastercardId($this->userId, $this->masterCardId);
    $userCardId = $cardDetail['user_card_id'];

    if(empty($cardDetail))
    {
      $this->setResponse('CARD_NOT_FOUND_IN_COLLECTION');
      return new ArrayObject();
    }

    //Check maximum card already is there in deck
    if(count($deckList) == MAX_CARD_IN_DECK && $this->replaceWithCard == 0)
    {
      $this->setResponse('MAX_CARD_IN_DECK_EXCEEDED');
      return new ArrayObject();
    }
    //if(($this->replaceWithCard > 0)  && $cardDetail['is_deck'] != DECK_ACTIVE)
    if(($this->replaceWithCard > 0)) 
    {
      $replaceCardDetail = $cardLib->getUserCardDetailForMastercardId($this->userId, $this->replaceWithCard);
      if(empty($replaceCardDetail))
      {
        $this->setResponse('REPLACE_CARD_NOT_FOUND');
        return new ArrayObject();
      }
      $cardLib->updateUserCard($cardDetail['user_card_id'], array("is_deck" => CONTENT_ACTIVE));
      $cardLib->updateUserCard($replaceCardDetail['user_card_id'], array("is_deck" => CONTENT_INACTIVE));
      $userCardId = $cardDetail['user_card_id'];
    }

    $this->setResponse('SUCCESS');
    return array('user_card_id' => $userCardId);
  }
}
