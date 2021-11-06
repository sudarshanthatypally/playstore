<?php
class master{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getMasterLevelUpList($options=array())
  {
    $sql = "SELECT *
            FROM master_level_up";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getMasterCardLevelUpList($options=array())
  {
    $sql = "SELECT *
            FROM master_card_level_upgrade";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getMasterStadiumList($options=array())
  {
    $sql = "SELECT *
            FROM master_stadium";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getStadiumDetail($stadiumId, $options=array())
  {
    $sql = "SELECT *
            FROM master_stadium
            WHERE master_stadium_id = :stadiumId";

    $result = database::doSelectOne($sql, array('stadiumId' => $stadiumId));
    return $result;
  }

  public function getMasterLevelUpDetail($masterLevelUpId, $options=array())
  {
    $sql = "SELECT *
            FROM master_level_up
            WHERE master_level_up_id = :masterLevelUpId";

    $result = database::doSelectOne($sql, array('masterLevelUpId' => $masterLevelUpId));
    return $result;
  }

  public function updateMasterStadium($stadiumId, $options=array())
  {
    $sql = "UPDATE master_stadium SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE master_stadium_id =:stadiumId";
    $options['stadiumId'] = $stadiumId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function updateMasterLevelUp($masterLevelUpId, $options=array())
  {
    $sql = "UPDATE master_level_up SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE master_level_up_id =:masterLevelUpId";
    $options['masterLevelUpId'] = $masterLevelUpId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function insertMasterStadium($options=array())
  {
    $sql = "INSERT INTO master_stadium ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function insertMasterLevelUp($options=array())
  {
    $sql = "INSERT INTO master_level_up ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getStadiumIdBasedOnRelics($relics)
  {
    $sql = "SELECT *
            FROM master_stadium
            WHERE relics_count_min <= :relics AND relics_count_max >= :relics";

    $result = database::doSelectOne($sql, array('relics' => $relics));
    return $result;
  }

  public function getMasterCardLevelUpgradeList($options=array())
  {
    $sql = "SELECT *
            FROM master_card_level_upgrade";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getMasterCardLevelUpgradeDetail($masterCardLevelUpgradeId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card_level_upgrade
            WHERE master_card_level_upgrade_id = :masterCardLevelUpgradeId ";

    $result = database::doSelectOne($sql, array('masterCardLevelUpgradeId' => $masterCardLevelUpgradeId));
    return $result;
  }

  public function updateMasterCardLevelUpgrade($masterCardLevelUpgradeId, $options=array())
  {
    $sql = "UPDATE master_card_level_upgrade SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE master_card_level_upgrade_id = :masterCardLevelUpgradeId";
    $options['masterCardLevelUpgradeId'] = $masterCardLevelUpgradeId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function insertMasterCardLevelUpgrade($options=array())
  {
    $sql = "INSERT INTO master_card_level_upgrade ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function deleteMasterCard($masterCardId, $options=array())
  {
    $sql = "DELETE FROM master_card
            WHERE master_card_id = :masterCardId";

	  $result = database::doDelete($sql, array('masterCardId' => $masterCardId));
    return $result;
  }

  public function deleteMasterCardLevelUpgrade($masterCardLevelUpgradeId, $options=array())
  {
    $sql = "DELETE FROM master_card_level_upgrade
            WHERE master_card_level_upgrade_id = :masterCardLevelUpgradeId";

	  $result = database::doDelete($sql, array('masterCardLevelUpgradeId' => $masterCardLevelUpgradeId));
    return $result;
  }

  public function deleteMasterStadium($masterStadiumId, $options=array())
  {
    $sql = "DELETE FROM master_stadium
            WHERE master_stadium_id = :masterStadiumId";

	  $result = database::doDelete($sql, array('masterStadiumId' => $masterStadiumId));
    return $result;
  }

  public function deleteMasterLevelUp($masterLevelUpId, $options=array())
  {
    $sql = "DELETE FROM master_level_up
            WHERE master_level_up_id = :masterLevelUpId";

	  $result = database::doDelete($sql, array('masterLevelUpId' => $masterLevelUpId));
    return $result;
  }

  public function deleteCardProperty($cardPropertyId, $options=array())
  {
    $sql = "DELETE FROM card_property
            WHERE card_property_id = :cardPropertyId";

	  $result = database::doDelete($sql, array('cardPropertyId' => $cardPropertyId));
    return $result;
  }

  public function insertCardProperty($options=array())
  {
    $sql = "INSERT INTO card_property";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getMasterCardList($options=array())
  {
    $sql = "SELECT *
            FROM master_card";

	  $result = database::doSelect($sql);
    return $result;
  }

  public function getMasterCardPropertyDetail($cardPropertyId, $options=array())
  {
    $sql = "SELECT cp.*, mc.title
            FROM card_property cp
            INNER JOIN master_card mc ON cp.master_card_id = mc.master_card_id
            WHERE card_property_id = :cardPropertyId";

	  $result = database::doSelectOne($sql, array('cardPropertyId' => $cardPropertyId));
    return $result;
  }

  public function getCardMasterPropertyList($options=array())
  {
    $sql = "SELECT *
            FROM card_master_property";

	  $result = database::doSelect($sql);
    return $result;
  }

  public function checkMasterCardPropertyExist($masterCardId, $propertyId, $options=array())
  {
    $sql = "SELECT *
            FROM card_property
            WHERE master_card_id = :masterCardId AND property_id = :propertyId";

	  $result = database::doSelectOne($sql, array('masterCardId' => $masterCardId, 'propertyId' => $propertyId ));
    return $result;
  }

  public function getCardPropertyLevelList($cardPropertyId, $masterCardId, $options=array())
  {
    $sql = "SELECT *
            FROM card_property_level_upgrade
            WHERE master_card_id = :masterCardId AND card_property_id = :cardPropertyId";

	  $result = database::doSelect($sql, array('masterCardId' => $masterCardId, 'cardPropertyId' => $cardPropertyId ));
    return $result;
  }

  public function getCardPropertyLevelUpgradeDetail($cardPropertyLevelUpgradeId, $options=array())
  {
    $sql = "SELECT card_property_level_upgrade.*, master_card.title
            FROM card_property_level_upgrade
            INNER JOIN master_card ON card_property_level_upgrade.master_card_id = master_card.master_card_id
            WHERE card_property_level_upgrade_id = :cardPropertyLevelUpgradeId";

	  $result = database::doSelectOne($sql, array('cardPropertyLevelUpgradeId' => $cardPropertyLevelUpgradeId));
    return $result;
  }

  public function updateCardProperty($cardPropertyId, $options=array())
  {
    $sql = "UPDATE card_property SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE card_property_id = :cardPropertyId";
    $options['cardPropertyId'] = $cardPropertyId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function updateCardPropertyLevel($cardPropertyLevelUpgradeId, $options=array())
  {
    $sql = "UPDATE card_property_level_upgrade SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE card_property_level_upgrade_id = :cardPropertyLevelUpgradeId";
    $options['cardPropertyLevelUpgradeId'] = $cardPropertyLevelUpgradeId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function deleteCardPropertyForMasterCard($masterCardId, $options=array())
  {
    $sql = "DELETE FROM card_property
            WHERE master_card_id = :masterCardId";

	  $result = database::doDelete($sql, array('masterCardId' => $masterCardId));
    return $result;
  }

  public function deleteCardPropertyLevelForMasterCard($masterCardId, $options=array())
  {
    $sql = "DELETE FROM card_property_level_upgrade
            WHERE master_card_id = :masterCardId";

	  $result = database::doDelete($sql, array('masterCardId' => $masterCardId));
    return $result;
  }

  public function insertCardPropertyLevelUpgrade($options=array())
  {
    $sql = "INSERT INTO card_property_level_upgrade";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function deleteCardPropertyLevel($cardPropertyLevelUpgradeId, $options=array())
  {
    $sql = "DELETE FROM card_property_level_upgrade
            WHERE card_property_level_upgrade_id = :cardPropertyLevelUpgradeId";

	  $result = database::doDelete($sql, array('cardPropertyLevelUpgradeId' => $cardPropertyLevelUpgradeId));
    return $result;
  }

  public function getDestroyedStadiumIdBasedOnRelics($relics)
  {

    $sql = "SELECT *
            FROM master_stadium
            WHERE destroys_at >=:relics";

    $result = database::doSelectOne($sql, array('relics' => $relics));
    return $result;
  }
}
