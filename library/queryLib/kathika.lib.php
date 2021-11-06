<?php
class kathika{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  } 

  public function getKathikaList($options=array())
  {
    $sql = "SELECT *
            FROM kathika";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getkathikaDetails($kathikaId){
    $sql = "SELECT *
            FROM kathika
            WHERE kathika_id = :kathikaId";

    $result = database::doSelectOne($sql, array('kathikaId' => $kathikaId));
    return $result;
  }

  public function getKathikaUnlockedList($userId){
    $sql = "SELECT k.kathika_id, k.chaptername, k.url_link,k.img_link, k.required_crystal_amount,
              (CASE WHEN k.status THEN kp.status 
                ELSE 'no status'
                  END ) AS status
              FROM kathika as k 
              LEFT JOIN kathika_property as kp 
              ON k.kathika_id=kp.kathika_id
              WHERE kp.user_id = :userId";
    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }

   public function checkKathikaDetails($kathikaId, $userId){
    $sql = "SELECT *
            FROM kathika_property
            WHERE kathika_id = :kathikaId AND user_id= :userId";

    $result = database::doSelectOne($sql, array('kathikaId' => $kathikaId, 'userId' => $userId));
    return $result;
  }
  public function unlockUserKathika($options=array()){
    $sql = "INSERT INTO kathika_property";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

}