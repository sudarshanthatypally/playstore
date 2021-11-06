<?php
/**
  * Author : Sudarshan Thatypally
  * Date   : 04-03-2021
  * Description : Handles mongo db connection and data transfer
  */

class mongoLib
{
  static public $db;
 
  public static function init($config)
  {
    $connection = new MongoClient("mongodb://".$config['mongo_server'].":".$config['mongo_port']);
    self::$db = $connection->$config['mongo_db_name'];

    return (self::$db)?true:false;
  }
}