<?php
class deck{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getDeckList($options=array())
  {
    $sql = "SELECT *
            FROM deck";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getDeckDetail($deckId, $options=array())
  {
    $sql = "SELECT *
            FROM deck
            WHERE deck_id=:deckId";

    $result = database::doSelectOne($sql, array('deckId'=>$deckId));
    return $result;
  }

  public function insertDeck($options=array())
  {
    $sql = "INSERT INTO deck ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function updateDeck($deckId, $options=array())
  {
    $sql = "UPDATE deck SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE deck_id =:deckId";
    $options['deckId'] = $deckId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function deleteDeck($deckId, $options=array())
  {
    $sql = "DELETE FROM deck
            WHERE deck_id = :deckId";

	  $result = database::doDelete($sql, array('deckId'=>$deckId));
    return $result;
  }

  public function getUserDeckDetail($userId, $options=array())
  {
    $sql = "SELECT *
            FROM user_deck
            WHERE user_id=:userId";

    $result = database::doSelectOne($sql, array('userId'=>$userId));
    return $result;
  }

  public function insertUserDeck($options=array())
  {
    $sql = "INSERT INTO user_deck ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function updateUserDeck($deckId, $options=array())
  {
    $sql = "UPDATE user_deck SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_deck_id =:deckId";
    $options['deckId'] = $deckId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
}