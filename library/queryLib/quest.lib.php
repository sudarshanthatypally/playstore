<?php
class quest{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getMasterQuestDetail($options=array())
  {
    $sql = "SELECT *
            FROM master_quest
            WHERE status=1";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getMasterCardListWithStadium($options=array())
  {
    $sql = "SELECT mc.master_card_id, mc.title,mc.card_max_level,mc.card_rarity_type,mc.is_available,mc.card_description,mc.bundlename,mc.android_bundlehash,mc.android_version_id, mc.ios_version_id,mc.android_bundlecrc,mc.ios_bundlehash,mc.ios_bundlecrc, mc.card_type, mc.is_card_default,ms.title as stadium_title, ms.master_stadium_id
            FROM master_card mc
            LEFT JOIN master_stadium ms ON mc.master_stadium_id = ms.master_stadium_id
            ORDER BY mc.master_card_id";
 
    $result = database::doSelect($sql);
    return $result;
  }

    public function getCardPrevious($userId){
    $sql = "SELECT uc.*
            FROM user_card uc
            WHERE uc.user_id=:userId";
    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }

  public function getPercentageofCardwithMasterId($masterId){
    $sql = "SELECT master_card_id,probability
            FROM master_card_probability 
            WHERE master_stadium_id=:masterId AND probability <> 0";
    $result = database::doSelect($sql, array('masterId'=>$masterId));
    return $result;
  }

  public function getinventoryForQuestOne($userId){
    $sql = "SELECT * 
            FROM quest_inventory 
            WHERE quest_id=1 AND user_id=:userId AND TIME > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = database::doSelect($sql, array('userId'=>$userId));
    return $result;
  }
  
  
  public function getMasterCardDetail($cardId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE master_card_id = :cardId";

    $result = database::doSelectOne($sql, array('cardId' => $cardId));
    return $result;
  }

  public function insertMasterQuestInventory($options=array())
  {
    $sql = "INSERT INTO quest_inventory ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function updateMasterCard($masterCardId, $options=array())
  {
    $sql = "UPDATE master_card SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE master_card_id =:masterCardId";
    $options['masterCardId'] = $masterCardId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function getCardDetails($masterStadiumId, $cardType, $cardRarityType){
    $sql = "SELECT * 
            FROM master_card 
            WHERE master_stadium_id<=:masterStadiumId 
              AND card_type=:cardType 
              AND card_rarity_type=:cardRarityType";
    $result = database::doSelect($sql, array('masterStadiumId'=>$masterStadiumId, 'cardType'=>$cardType, 'cardRarityType'=>$cardRarityType));
    return $result;
  }
}
