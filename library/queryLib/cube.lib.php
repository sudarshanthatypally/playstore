<?php
class cube{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getCubeProbabilityDetailForStadium($cubeId, $stadiumId, $randomCubeProbability, $options=array())
  {
    $sql = "SELECT *
            FROM master_cube_probability
            WHERE cube_id = :cubeId AND master_stadium_id = :stadiumId AND min_value <= :randomCubeProbability AND  max_value >= :randomCubeProbability";

    $result = database::doSelectOne($sql, array('cubeId'=>$cubeId, 'stadiumId'=>$stadiumId, 'randomCubeProbability' => $randomCubeProbability));
    return $result;
  }

  public function getRandomCubeDetailForStadium($cubeId, $stadiumId, $options=array())
  {
    $sql = "SELECT *
            FROM master_cube_probability
            WHERE cube_id = :cubeId AND master_stadium_id = :stadiumId ";

    $result = database::doSelectOne($sql, array('cubeId'=>$cubeId, 'stadiumId'=>$stadiumId));
    return $result;
  }

  public function getCubeRewardDetailForStadium($cubeId, $stadiumId, $options=array())
  {
    $sql = "SELECT *
            FROM master_cube_reward
            WHERE cube_id = :cubeId AND master_stadium_id <= :stadiumId ORDER BY master_stadium_id DESC LIMIT 1";

    $result = database::doSelectOne($sql, array('cubeId'=>$cubeId, 'stadiumId'=>$stadiumId));
    return $result;
  }

  public function CheckEligibilityOfCubeReward($userId, $options=array())
  {
    $sql = "SELECT *
            FROM user_reward
            WHERE user_id = :userId AND status <> :rewardStatus AND cube_id IN (".CUBE_DYNAMITE.", ".CUBE_BOMB.", ".CUBE_ROCKET.",".CUBE_FIRECRACKER.",".CUBE_METALBOMB.")";
 
    $result = database::doSelect($sql, array('userId'=>$userId, 'rewardStatus'=> CONTENT_CLOSED));
    return $result;
  }
}
