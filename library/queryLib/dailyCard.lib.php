<?php
class dailyCard{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getDailyCardList($options=array())
  {
    $sql = "SELECT *
            FROM dailyCard";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getDailyCardDetail($dailyCardId, $options=array())
  {
    $sql = "SELECT *
            FROM dailyCard
            WHERE dailyCard_id=:dailyCardId";

    $result = database::doSelectOne($sql, array('dailyCardId'=>$dailyCardId));
    return $result;
  }

  public function insertDailyCard($options=array())
  {
    $sql = "INSERT INTO dailyCard ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function updateDailyCard($dailyCardId, $options=array())
  {
    $sql = "UPDATE dailyCard SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE dailyCard_id =:dailyCardId";
    $options['dailyCardId'] = $dailyCardId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function deleteDailyCard($dailyCardId, $options=array())
  {
    $sql = "DELETE FROM dailyCard
            WHERE dailyCard_id = :dailyCardId";

	  $result = database::doDelete($sql, array('dailyCardId'=>$dailyCardId));
    return $result;
  }


  public function getUserDailyCardDetail($userId, $options=array())
  {
    $sql = "SELECT *
            FROM user_daily_cards
            WHERE user_id =:userId 
            ORDER BY created_at DESC";

    $result = database::doSelectOne($sql, array('userId' => $userId));
    return $result;
  }

  public function insertUserDailyCard($options=array())
  {
    $sql = "INSERT INTO user_daily_cards ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function updateUserDailyCard($userId, $options=array())
  {
    $sql = "UPDATE user_daily_cards SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_id =:userId";
    $sql .= " ORDER BY created_at DESC LIMIT 1";
    $options['userId'] = $userId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
}