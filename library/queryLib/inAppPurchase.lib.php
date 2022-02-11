<?php
class inAppPurchase{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getMasterCubeInventoryListBasedOnStadium($masterStadiumId, $options=array())
  {
    $sql = "SELECT *
            FROM master_cube_inventory WHERE master_stadium_id <= :masterStadiumId ORDER BY master_stadium_id DESC, cube_id ASC LIMIT 3";

    $result = database::doSelect($sql, array('masterStadiumId' => $masterStadiumId));
    return $result;
  }

  public function getMasterCubeInventoryDetail($masterCubeInventoryId, $options=array())
  {
    $sql = "SELECT *
            FROM master_cube_inventory WHERE master_cube_inventory_id = :masterCubeInventoryId";

    $result = database::doSelectOne($sql, array('masterCubeInventoryId' => $masterCubeInventoryId));
    return $result;
  }

  public function getMasterInventoryListBasedOnType($type, $options=array())
  {
    $sql = "SELECT *
            FROM master_inventory WHERE type = :type";

    $result = database::doSelect($sql, array('type' => $type));
    return $result;
  }

  public function getMasterInventoryDetail($masterInventoryId, $options=array())
  {
    $sql = "SELECT *
            FROM master_inventory WHERE master_inventory_id = :masterInventoryId";

    $result = database::doSelectOne($sql, array('masterInventoryId' => $masterInventoryId));
    return $result;
  }

  public function getMasterGoldInventoryList($options=array())
  {
    $sql = "SELECT *
            FROM master_gold_inventory";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getMasterGoldInventoryDetail($masterGoldInventoryId, $options=array())
  {
    $sql = "SELECT *
            FROM master_gold_inventory WHERE master_gold_inventory_id = :masterGoldInventoryId";

    $result = database::doSelectOne($sql, array('masterGoldInventoryId' => $masterGoldInventoryId));
    return $result;
  }

  public function insertInAppPurchase($options=array())
  {
    $sql = "INSERT INTO inAppPurchase_transaction";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function insertInAppPurchaseInventory($options=array())
  {
    $sql = "INSERT INTO inAppPurchase_inventory";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function getMasterInventoryList($options=array())
  {
    $sql = "SELECT *
            FROM master_inventory";

    $result = database::doSelect($sql);
    return $result;
  }

}
