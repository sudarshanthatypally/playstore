<?php
class card{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
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
  
  public function getMasterCardDetail($cardId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE master_card_id = :cardId";

    $result = database::doSelectOne($sql, array('cardId' => $cardId));
    return $result;
  }

  public function insertMasterCard($options=array())
  {
    $sql = "INSERT INTO master_card ";
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
  public function getCardFromCardType($masterStadiumId, $cardType){
    $sql = "SELECT * 
            FROM master_card 
            WHERE master_stadium_id<=:masterStadiumId 
              AND card_type=:cardType AND is_available=1";
      $result = database::doSelect($sql, array('masterStadiumId'=>$masterStadiumId, 'cardType'=>$cardType));
      return $result;
  } 
  public function getCardFromMasterStadiumId($masterStadiumId){
    $sql = "SELECT * 
            FROM master_card 
            WHERE master_stadium_id<=:masterStadiumId";
      $result = database::doSelect($sql, array('masterStadiumId'=>$masterStadiumId));
      return $result;
  }
  public function insertUserCard($options=array())
  {
    $sql = "INSERT INTO user_card ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function getUserCardList($userId, $options=array())
  {
    $sql = "SELECT *
            FROM user_card
            WHERE user_id = :userId";

    $result = database::doSelect($sql, array('userId'=>$userId));
    return $result;
  }

  public function getUserCardDetail($userCardId, $options=array())
  {
    $sql = "SELECT *
            FROM user_card
            WHERE user_card_id = :userCardId";

    $result = database::doSelectOne($sql, array('userCardId' => $userCardId));
    return $result;
  }

  public function getUserCardWithProperty($userId, $masterCardId, $options=array())
  {
    $sql = "SELECT ucp.user_card_property_id, cp.card_property_id, ucp.user_card_property_value, cp.property_id,cp.property_name,cp.is_child,cp.is_default
            FROM user_card uc
            INNER JOIN user_card_property ucp ON uc.user_card_id = ucp.user_card_id
            INNER JOIN card_property cp ON ucp.card_property_id = cp.card_property_id
            INNER JOIN master_card mc ON uc.master_card_id = mc.master_card_id
            WHERE uc.master_card_id = :masterCardId AND uc.user_id = :userId";

    $result = database::doSelect($sql, array('userId' => $userId, 'masterCardId' => $masterCardId));
    return $result;
  }

  public function updateUserCard($userCardId, $options=array())
  {
    $sql = "UPDATE user_card SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_card_id =:userCardId";
    $options['userCardId'] = $userCardId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function getUserCardForActiveDeck($userId, $isDeck, $limit=8, $options=array())
  {
    $sql = "SELECT uc.*, mc.title, mc.card_type, mc.is_available, mc.card_description, mc.card_rarity_type, mc.bundlename,mc.android_bundlehash,mc.android_bundlecrc,mc.ios_bundlehash,mc.ios_bundlecrc
            FROM user_card uc
            INNER JOIN master_card mc ON uc.master_card_id = mc.master_card_id
            WHERE uc.user_id = :userId AND uc.is_deck = :isDeck
            ORDER BY uc.user_card_id
            LIMIT ".$limit;

    $result = database::doSelect($sql, array('userId' => $userId, 'isDeck' => $isDeck));
    return $result;
  }

  public function getMasterCardLevelUpgradeForCardCount($levelId, $rarityType, $options=array())
  {
    $sql = "SELECT *
            FROM master_card_level_upgrade
            WHERE level_id = :levelId AND rarity_type = :rarityType";

    $result = database::doSelectOne($sql, array('levelId' => $levelId, 'rarityType' => $rarityType));
    return $result;
  }

  public function getMasterCardRarityListBasedOnStadium($masterStadiumId, $rarityType, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE master_stadium_id <= :masterStadiumId AND card_rarity_type = :rarityType";

    $result = database::doSelect($sql, array('masterStadiumId' => $masterStadiumId, 'rarityType' => $rarityType));
    return $result;
  }

  public function getMasterCardBasedOnStadiumAndProbability($masterStadiumId, $probability, $excludeCardId, $limit, $options=array())
  {
    $sql = "SELECT mcp.master_card_id
            FROM master_card_probability mcp
            WHERE mcp.master_stadium_id <= :masterStadiumId AND mcp.probability <=:probability
            AND mcp.master_card_id NOT IN(".$excludeCardId.")
            ORDER BY RAND()
            LIMIT ".$limit;

    $result = database::doSelect($sql, array('masterStadiumId' => $masterStadiumId, 'probability' => $probability));
    return $result;
  }

  /*public function getMasterCardListBasedOnStadiumAndRarity($masterStadiumId, $rarityType, $limit, $randomCardProbability, $excludeCardId, $options=array())
  {
    $sql = "SELECT mc.master_card_id
            FROM master_card mc 
            WHERE mc.card_rarity_type =:rarityType AND mc.master_stadium_id = :masterStadiumId AND mc.master_card_id NOT IN(".$excludeCardId.")
            ORDER BY RAND()
            LIMIT ".$limit;

    $result = database::doSelect($sql, array('masterStadiumId' => $masterStadiumId, 'rarityType' => $rarityType));
    return $result;
  }*/

  public function getMasterCardBasedOnStadiumAndRarity($masterStadiumId, $rarityType, $randomCardProbability, $excludeCardId, $options=array())
  {
    $sql = "SELECT mcp.master_card_id
            FROM master_card_probability mcp
            INNER JOIN master_card ON mcp.master_card_id = master_card.master_card_id
            WHERE master_card.card_rarity_type = :rarityType
            AND mcp.master_stadium_id <= :masterStadiumId  AND probability >=:randomCardProbability AND mcp.master_card_id NOT IN(".$excludeCardId.")";

    $result = database::doSelectOne($sql, array('masterStadiumId' => $masterStadiumId, 'rarityType' => $rarityType, 'randomCardProbability' => $randomCardProbability));
    return $result;
  }
 
  public function checkCardRarity($cardId, $options=array())
  {
    $sql = "SELECT card_rarity_type
            FROM master_card
            WHERE master_card_id = :cardId"; 

    $result = database::doSelect($sql, array('cardId' => $cardId));
    
    foreach ($result as $crc) {
      $card_rarity_t = $crc['card_rarity_type'];
    }
    return $card_rarity_t;
  }
 
  public function getMasterCardProbabilityListBasedOnStadiumAndRarity($masterStadiumId, $rarityType, $probability, $excludeCardId, $limit, $options=array())
  {
    $sql = "SELECT mcp.master_card_id
            FROM master_card_probability mcp
            INNER JOIN master_card mc ON mcp.master_card_id = mc.master_card_id
            WHERE mc.card_rarity_type = :rarityType AND mcp.master_stadium_id <= :masterStadiumId AND mcp.master_card_id NOT IN(".$excludeCardId.")
            AND probability <= :probability
            ORDER BY RAND()
            LIMIT ".$limit;

    $result = database::doSelect($sql, array('masterStadiumId' => $masterStadiumId, 'rarityType' => $rarityType, 'probability' => $probability));
    return $result;
  }
  
public function getMasterCardListBasedOnStadiumAndRarity($masterStadiumId, $rarityType, $probability, $excludeCardId=0, $limit, $options=array())
  { 
    $sql = "SELECT mc.master_card_id
            FROM master_card mc 
            WHERE mc.card_rarity_type =:rarityType AND is_available=1 AND mc.master_stadium_id <= :masterStadiumId AND mc.master_card_id NOT IN(".$excludeCardId.")
            ORDER BY RAND()
            LIMIT ".$limit;

    $result = database::doSelect($sql, array('masterStadiumId' => $masterStadiumId, 'rarityType' => $rarityType));
    return $result;
  }
  public function getMasterCardListBasedOnStadiumAndRarityWithVersion($masterStadiumId, $rarityType, $probability, $excludeCardId=0, $limit, $andVer, $iosVer, $options=array())
  { 
    /*$sql = "SELECT mc.master_card_id
            FROM master_card mc 
            WHERE mc.card_rarity_type =:rarityType AND is_available=1 AND mc.master_stadium_id <= :masterStadiumId AND mc.master_card_id NOT IN(".$excludeCardId.")
            ORDER BY RAND()
            LIMIT ".$limit;*/
    if(!empty($andVer)){
      $sql = "SELECT mc.master_card_id
            FROM master_card mc 
            WHERE mc.card_rarity_type =:rarityType AND 
                  mc.master_stadium_id <= :masterStadiumId AND 
                  INET_ATON(mc.android_version_id)<=INET_ATON('".$andVer."') AND 
                  mc.master_card_id NOT IN(".$excludeCardId.")
            ORDER BY RAND()
            LIMIT ".$limit;
    }elseif(!empty($iosVer)){
      $sql = "SELECT mc.master_card_id
            FROM master_card mc 
            WHERE mc.card_rarity_type =:rarityType AND 
                  mc.master_stadium_id <= :masterStadiumId AND 
                  INET_ATON(mc.ios_version_id)<=INET_ATON('".$iosVer."') AND 
                  mc.master_card_id NOT IN(".$excludeCardId.")
            ORDER BY RAND()
            LIMIT ".$limit;
    }else{
      $sql = "SELECT mc.master_card_id
            FROM master_card mc 
            WHERE mc.card_rarity_type =:rarityType AND is_available=1 AND mc.master_stadium_id <= :masterStadiumId AND mc.master_card_id NOT IN(".$excludeCardId.")
            ORDER BY RAND()
            LIMIT ".$limit;
    }
    $result = database::doSelect($sql, array('masterStadiumId' => $masterStadiumId, 'rarityType' => $rarityType));
    return $result;
  }
  public function getMasterLevelUpXpDetail($levelId, $options=array())
  {
    $sql = "SELECT *
            FROM master_level_up
            WHERE level_id = :levelId ";

    $result = database::doSelectOne($sql, array('levelId' => $levelId));
    return $result;
  }

  public function getMasterLevelUpXpForUser($userId, $levelId, $options=array())
  {
    $sql = "SELECT user.user_id, user.xp, master_level_up.*
            FROM user
            INNER JOIN master_level_up ON master_level_up.level_id = :levelId
            WHERE user.user_id = :userId
            AND user.xp >= master_level_up.xp_to_next_level ";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'levelId' => $levelId));
    return $result;
  }

  public function getUserCardListForUserId($userId, $options=array())
  {
    $sql = "SELECT user_card.*, master_card.title, master_card.master_stadium_id, master_card.is_available, master_card.card_rarity_type, master_card.card_type, master_card.android_version_id, master_card.ios_version_id, master_card.card_description
            FROM user_card
            INNER JOIN master_card ON user_card.master_card_id = master_card.master_card_id
            WHERE user_card.user_id = :userId";

    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }
  public function getUserCardListForRequestedUser($userId, $is_sunday=0, $options=array())
  {
    if($is_sunday==1){
      $sql = "SELECT user_card.*, master_card.title, master_card.master_stadium_id, master_card.is_available, master_card.card_rarity_type, master_card.card_type, master_card.android_version_id, master_card.ios_version_id, master_card.card_description
            FROM user_card
            INNER JOIN master_card ON user_card.master_card_id = master_card.master_card_id
            WHERE user_card.user_id = :userId AND (master_card.card_rarity_type=1 || master_card.card_rarity_type=2 || master_card.card_rarity_type=3)";
    }else{
      $sql = "SELECT user_card.*, master_card.title, master_card.master_stadium_id, master_card.is_available, master_card.card_rarity_type, master_card.card_type, master_card.android_version_id, master_card.ios_version_id, master_card.card_description
            FROM user_card
            INNER JOIN master_card ON user_card.master_card_id = master_card.master_card_id
            WHERE user_card.user_id = :userId AND (master_card.card_rarity_type=1 || master_card.card_rarity_type=2)";
    }
    

    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }
  public function getDefaultMasterCardList($options=array())
  {
    /*$sql = "SELECT *
            FROM master_card
            WHERE is_card_default = :defaultCard
            ORDER BY master_card_id";*/
    $sql = "SELECT mclu.rarity_type,mclu.level_id,mc.*
    FROM master_card mc
    LEFT JOIN master_card_level_upgrade mclu ON mc.card_rarity_type=mclu.rarity_type
    WHERE mc.is_card_default =:defaultCard GROUP BY mc.master_card_id ORDER BY mc.master_card_id ASC";

    $result = database::doSelect($sql, array('defaultCard' => CONTENT_ACTIVE));
    return $result;
  }

  public function getMasterCardPropertyList($masterCardId, $options=array())
  {
    $sql = "SELECT *
            FROM card_property
            WHERE master_card_id = :masterCardId";

    $result = database::doSelect($sql, array('masterCardId' => $masterCardId));
    return $result;
  }
  public function getMasterCardPropertyListId($masterCardId,$levelId, $options=array())
  {
    $sql = "SELECT cp.master_card_id,cp.card_property_id,cp.property_id,cp.property_name, cp.is_child, cp.is_default,cp.status,cplu.card_property_value
            FROM card_property cp
            LEFT JOIN card_property_level_upgrade cplu ON cplu.card_property_id=cp.card_property_id
            WHERE cp.master_card_id =:masterCardId AND cplu.level_id=:levelId ORDER BY cp.card_property_id ASC";

    $result = database::doSelect($sql, array('masterCardId' => $masterCardId, 'levelId'=> $levelId));
    return $result;
  }
  public function insertUserCardProperty($options=array())
  {
    $sql = "INSERT INTO user_card_property ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  
  public function getUserCardDetailForMastercardId($userId, $masterCardId, $options=array())
  {
    $sql = "SELECT uc.*, mc.card_rarity_type, mc.card_max_level, mc.gold
            FROM user_card uc
            INNER JOIN master_card mc  ON uc.master_card_id = mc.master_card_id
            WHERE uc.user_id = :userId AND uc.master_card_id = :masterCardId";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'masterCardId' => $masterCardId));
    
    return $result;
  } 

  public function getUserCardDetailForMastercardIdIfNull($masterCardId, $options=array()) 
  {
      $sql = "SELECT mclu.*,mc.*
              FROM master_card mc
              LEFT JOIN master_card_level_upgrade mclu ON mc.card_rarity_type=mclu.rarity_type
              WHERE mc.master_card_id=:masterCardId
              GROUP BY mc.master_card_id 
              ORDER BY mc.master_card_id ASC";

    $result = database::doSelectOne($sql, array('masterCardId' => $masterCardId));
    return $result;
    
  }
  public function getCardPropertyLevelUpgradeDetail($masterCardId, $levelId, $options=array())
  {
    $sql = "SELECT *
            FROM card_property_level_upgrade
            WHERE master_card_id = :masterCardId AND level_id = :levelId";

    $result = database::doSelectOne($sql, array('masterCardId' => $masterCardId, 'levelId' => $levelId));
    return $result;
  }

  public function updateUserCardProperty($userCardpropertyId, $options=array())
  {
    $sql = "UPDATE user_card_property SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE user_card_property_id =:userCardpropertyId";
    $options['userCardpropertyId'] = $userCardpropertyId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }

  public function getCardPropertyValue($masterCardId, $levelId, $cardPropertyId, $options=array())
  {
    /*if(!empty($levelId)){
      $sql = "SELECT *
            FROM card_property_level_upgrade
            WHERE master_card_id = :masterCardId AND level_id = :levelId AND card_property_id = :cardPropertyId";
    }else{*/
      $sql = "SELECT *
            FROM card_property_level_upgrade
            WHERE master_card_id = :masterCardId AND level_id <= :levelId AND card_property_id = :cardPropertyId
            ORDER BY level_id DESC LIMIT 1";
  //  }
    
    
    $result = database::doSelectOne($sql, array('masterCardId' => $masterCardId, 'levelId' => $levelId, 'cardPropertyId' => $cardPropertyId));
    return $result;
  }

  public function getCardPropertyWithMultipleLevelIdValue($masterCardId, $levelId, $altId,$cardPropertyId, $options=array())
  {
    /*$sql = "SELECT *
            FROM card_property_level_upgrade
            WHERE master_card_id = :masterCardId AND level_id = :levelId AND card_property_id = :cardPropertyId";*/
    $sql="SELECT *
            FROM card_property_level_upgrade
            WHERE master_card_id = :masterCardId AND level_id <= :levelId AND card_property_id = :cardPropertyId
            UNION
            SELECT *
            FROM card_property_level_upgrade
            WHERE master_card_id = :masterCardId AND level_id <= :altId AND card_property_id = :cardPropertyId
            ORDER BY level_id DESC LIMIT 1";
    $result = database::doSelectOne($sql, array('masterCardId' => $masterCardId, 'levelId' => $levelId,'altId'=>$altId, 'cardPropertyId' => $cardPropertyId));
    return $result;
  }

  public function getCardPropertyForUseCardId($userCardId, $options=array())
  {
    $sql = "SELECT *, user_card_property.user_card_property_value, property_id, property_name,card_property_value,is_default
            FROM user_card
            INNER JOIN user_card_property ON user_card.user_card_id = user_card_property.user_card_id
            INNER JOIN card_property ON user_card_property.card_property_id = card_property.card_property_id
            WHERE user_card.user_card_id = :userCardId";

    $result = database::doSelect($sql, array('userCardId' => $userCardId));
    return $result;
  }
  public function getCardPropertyForUseCardIdAndLevelIdAndCommonLevel($userCardId, $levelId, $basicLevelId, $options=array())
  {
   /* $sql = "SELECT uc.* , ucp.card_property_id,ucp.user_card_property_id, plu.card_property_value AS user_card_property_value, plu.card_property_value, cp.property_id, cp.property_name, is_default
            FROM user_card AS uc
            INNER JOIN user_card_property AS ucp ON ucp.user_card_id=uc.user_card_id
            INNER JOIN card_property_level_upgrade AS plu ON plu.card_property_id=ucp.card_property_id
            INNER JOIN card_property AS cp ON cp.card_property_id=ucp.card_property_id
            WHERE ucp.user_card_id=:userCardId AND plu.level_id=:levelId";*/

    $sql="WITH cte AS (
            SELECT plu.card_property_id,plu.card_property_value,plu.level_id,cp.property_id, cp.property_name, cp.is_child, cp.is_default, ucp.user_card_property_value,RANK() 
            OVER (PARTITION BY plu.card_property_id ORDER BY plu.level_id DESC) ran
            FROM user_card AS uc
            INNER JOIN card_property AS cp ON cp.master_card_id=uc.master_card_id
            INNER JOIN user_card_property AS ucp ON ucp.card_property_id=cp.card_property_id
            INNER JOIN card_property_level_upgrade AS plu ON plu.card_property_id=cp.card_property_id
            WHERE ucp.user_card_id=:userCardId AND uc.level_id IN (:levelId,:basicLevelId) AND plu.level_id IN (:levelId,:basicLevelId)  
            GROUP BY ucp.user_card_property_id)
          SELECT * FROM cte  WHERE ran=1";

    $result = database::doSelect($sql, array('userCardId' => $userCardId, 'levelId'=> $levelId, 'basicLevelId'=>$basicLevelId));
    return $result;
  }

  public function getCardPropertyForMasterCardAndLevelIdAndCommonLevel($masterCard, $levelId, $basicLevelId, $options=array())
  {
    $sql="WITH cte AS (
      SELECT DISTINCT plu.card_property_id,cp.master_card_id,cp.property_id,cp.property_name,plu.card_property_value,plu.level_id,cp.is_child,cp.show_info, cp.is_default,cp.status,RANK() 
      OVER (PARTITION BY plu.card_property_id ORDER BY plu.level_id DESC)  ran
      FROM card_property_level_upgrade AS plu
      INNER JOIN card_property AS cp ON cp.card_property_id=plu.card_property_id
      WHERE plu.master_card_id=:masterCard  AND plu.level_id IN (:levelId,:basicLevelId) )
      SELECT * FROM cte  WHERE ran=1";

    $result = database::doSelect($sql, array('masterCard' => $masterCard, 'levelId'=> $levelId, 'basicLevelId'=>$basicLevelId));
    return $result;
  }
  
  public function getCardPropertyForUseCardIdAndLevelId($userCardId, $levelId, $options=array())
  {
   /* $sql = "SELECT uc.* , ucp.card_property_id,ucp.user_card_property_id, plu.card_property_value AS user_card_property_value, plu.card_property_value, cp.property_id, cp.property_name, is_default
            FROM user_card AS uc
            INNER JOIN user_card_property AS ucp ON ucp.user_card_id=uc.user_card_id
            INNER JOIN card_property_level_upgrade AS plu ON plu.card_property_id=ucp.card_property_id
            INNER JOIN card_property AS cp ON cp.card_property_id=ucp.card_property_id
            WHERE ucp.user_card_id=:userCardId AND plu.level_id=:levelId";*/

    $sql="WITH cte AS (
            SELECT plu.card_property_id,plu.card_property_value,plu.level_id,cp.property_id, cp.property_name, cp.is_child, cp.is_default, ucp.user_card_property_value,RANK() 
            OVER (PARTITION BY plu.card_property_id ORDER BY plu.level_id DESC) ran
            FROM user_card AS uc
            INNER JOIN card_property AS cp ON cp.master_card_id=uc.master_card_id
            INNER JOIN user_card_property AS ucp ON ucp.card_property_id=cp.card_property_id
            INNER JOIN card_property_level_upgrade AS plu ON plu.card_property_id=cp.card_property_id
            WHERE ucp.user_card_id=72 AND uc.level_id IN (4,6) AND plu.level_id IN (4,6)  
            GROUP BY ucp.user_card_property_id)
          SELECT * FROM cte  WHERE ran=1";
  /*  $sql = "SELECT uc.*, cp.property_id, cp.property_name, cp.is_child, cp.is_default, plu.*
            FROM user_card AS uc
            LEFT JOIN card_property AS cp ON cp.master_card_id=uc.master_card_id
            LEFT JOIN user_card_property AS ucp ON ucp.card_property_id=cp.card_property_id
            LEFT JOIN card_property_level_upgrade AS plu ON plu.card_property_id=cp.card_property_id
            WHERE ucp.user_card_id=:userCardId AND uc.level_id=:levelId AND plu.level_id=:levelId
            GROUP BY cp.property_id";*/
    $result = database::doSelect($sql, array('userCardId' => $userCardId, 'levelId'=> $levelId));
    return $result;
  }
  public function getCardPropertyForUserIdAndLevelId($userId, $levelId, $options=array())
  {
    /*$sql = "SELECT uc.* , ucp.card_property_id,ucp.user_card_property_id, plu.card_property_value AS user_card_property_value, plu.card_property_value, cp.property_id, cp.property_name, is_default
            FROM user_card AS uc
            INNER JOIN user_card_property AS ucp ON ucp.user_card_id=uc.user_card_id
            INNER JOIN card_property_level_upgrade AS plu ON plu.card_property_id=ucp.card_property_id
            INNER JOIN card_property AS cp ON cp.card_property_id=ucp.card_property_id
            WHERE ucp.user_card_id=:userCardId AND plu.level_id=:levelId";*/
    $sql = "SELECT uc.*, cp.property_id, cp.property_name,cp.is_child, cp.is_default, plu.*
            FROM user_card AS uc
            LEFT JOIN card_property AS cp ON cp.master_card_id=uc.master_card_id
            LEFT JOIN card_property_level_upgrade AS plu ON plu.card_property_id=cp.card_property_id
            WHERE uc.user_id=:userId AND plu.level_id=:userId";
    $result = database::doSelect($sql, array('userId' => $userId, 'levelId'=> $levelId));
    return $result;
  }
public function getUserCardUnlockLevelOnRarityTypeAndMasterCardId($masterCardId, $options=array())
  {
    $sql = "SELECT level_id FROM master_card_level_upgrade 
            LEFT JOIN master_card ON master_card.card_rarity_type = rarity_type 
            WHERE master_card.master_card_id = :masterCardId ORDER BY level_id ASC LIMIT 1";

    $result = database::doSelectOne($sql, array('masterCardId' => $masterCardId));
    return $result;
  }
  public function getUserCardForUserIdAndMasterCardId($userId, $masterCardId, $options=array())
  {
    $sql = "SELECT *
            FROM user_card
            WHERE user_id = :userId AND master_card_id = :masterCardId";

    $result = database::doSelectOne($sql, array('userId' => $userId, 'masterCardId' => $masterCardId));
    return $result;
  }

  public function getMasterCardList($options=array())
  {
    $sql = "SELECT *
            FROM master_card";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getUserCardDeckList($userId, $options=array())
  {
    $sql = "SELECT user_card.*, master_card.title, master_card.card_rarity_type, master_card.card_type, master_card.card_description, master_card.bundlename,master_card.android_bundlehash,master_card.android_bundlecrc,master_card.ios_bundlehash,master_card.ios_bundlecrc   
            FROM user_card
            INNER JOIN master_card ON user_card.master_card_id = master_card.master_card_id
            WHERE user_id = :userId AND is_deck = :active";

    $result = database::doSelect($sql, array('userId'=>$userId, 'active'=>CONTENT_ACTIVE));
    return $result;
  }

  public function getMasterLevelUpXpForUserLevel( $levelId, $options=array())
  {
    $sql = "SELECT *
            FROM master_level_up
            WHERE level_id = :levelId";

    $result = database::doSelectOne($sql, array('levelId' => $levelId));
    return $result;
  }

  public function getMasterCardListForStadium($stadiumId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE master_stadium_id <=:stadiumId";

    $result = database::doSelect($sql,array('stadiumId' => $stadiumId));
    return $result;
  }
  public function getMasterCardListForStadiumWithUserId($userId, $options=array()){
    $sql = "SELECT mc.*
            FROM master_card mc
            INNER JOIN user_card uc ON uc.master_card_id = mc.master_card_id
            WHERE uc.user_id =:userId AND is_available=1";
    $result = database::doSelect($sql, array('userId' => $userId));
    return $result; 
  }
  public function getMasterCardListForStadiumWithUserIdWithVersion($userId, $andVer, $iosVer, $options=array()){
    /*$sql = "SELECT mc.*
            FROM master_card mc
            INNER JOIN user_card uc ON uc.master_card_id = mc.master_card_id
            WHERE uc.user_id =:userId";*/
    if(!empty($andVer)){
      $sql = "SELECT mc.*
              FROM master_card mc
              WHERE master_stadium_id <= (SELECT master_stadium_id FROM user WHERE user_id=:userId) AND INET_ATON(mc.android_version_id)<=INET_ATON('".$andVer."')";
    }elseif(!empty($iosVer)){
      $sql = "SELECT mc.*
              FROM master_card mc
              WHERE master_stadium_id <= (SELECT master_stadium_id FROM user WHERE user_id=:userId) AND INET_ATON(ios_version_id)<=INET_ATON('".$iosVer."')";
    }else{
      $sql = "SELECT mc.*
            FROM master_card mc
            WHERE master_stadium_id <= (SELECT master_stadium_id FROM user WHERE user_id=:userId) AND mc.is_available=1";
    }

    $result = database::doSelect($sql, array('userId' => $userId));
    return $result; 
  }
  public function getMasterCardListForUnlocking($stadiumId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE master_stadium_id =:stadiumId AND is_available=1";

    $result = database::doSelect($sql,array('stadiumId' => $stadiumId));
    return $result;
  }
  public function getMasterCardListForUnlockingWithVersion($stadiumId, $andVer, $iosVer, $options=array())
  {
    if(!empty($andVer)){
      $sql = "SELECT *
            FROM master_card
            WHERE master_stadium_id =:stadiumId AND INET_ATON(android_version_id)<=INET_ATON('".$andVer."')";
    }elseif(!empty($iosVer)){
      $sql = "SELECT *
            FROM master_card
            WHERE master_stadium_id =:stadiumId AND INET_ATON(ios_version_id)<=INET_ATON('".$iosVer."')";
    }else{
      $sql = "SELECT *
            FROM master_card
            WHERE master_stadium_id =:stadiumId AND is_available=1";
    }
    $result = database::doSelect($sql,array('stadiumId' => $stadiumId));
    return $result;
  }
  /*public function getMasterCardListForRequestWithVersion($stadiumId, $andVer, $iosVer, $options=array())
  {
    if(!empty($andVer)){
      $sql = "SELECT *
            FROM master_card
            WHERE INET_ATON(android_version_id)<=INET_ATON('".$andVer."')
            ORDER BY card_rarity_type,master_card_id ASC";
    }elseif(!empty($iosVer)){
      $sql = "SELECT *
            FROM master_card
            WHERE INET_ATON(ios_version_id)<=INET_ATON('".$iosVer."')
            ORDER BY card_rarity_type,master_card_id ASC";
    }else{
      $sql = "SELECT *
            FROM master_card
            WHERE is_available=1
            ORDER BY card_rarity_type,master_card_id ASC";
    }
    $result = database::doSelect($sql,array('stadiumId' => $stadiumId));
    return $result;
  }*/
  public function getMasterCardListForRequestWithVersion($andVer, $iosVer, $rarityId, $options=array())
  {
    if(!empty($andVer)){
      $sql = "SELECT *
            FROM master_card
            WHERE INET_ATON(android_version_id)<=INET_ATON('".$andVer."') AND is_available=1 AND card_rarity_type=:rarityId
            ORDER BY master_card_id ASC";
    }elseif(!empty($iosVer)){
      $sql = "SELECT *
            FROM master_card
            WHERE INET_ATON(ios_version_id)<=INET_ATON('".$iosVer."') AND is_available=1 AND card_rarity_type=:rarityId
            ORDER BY master_card_id ASC";
    }else{
      $sql = "SELECT *
            FROM master_card
            WHERE is_available=1 AND card_rarity_type=:rarityId
            ORDER BY master_card_id ASC";
    }
    $result = database::doSelect($sql,array('rarityId'=> $rarityId));
    return $result;
  }
  public function getFutureMasterCardList($userId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE is_available=0 AND master_card_id NOT IN 
              (SELECT master_card_id  
              FROM user_card where user_id =:userId)";

    $result = database::doSelect($sql,array('userId' => $userId));
    return $result;
  } 
  public function getFutureMasterCardListWithVersion($userId, $andVer, $iosVer, $options=array())
  {
    if(!empty($andVer)){
      $sql = "SELECT *
      FROM master_card
      WHERE android_version_id IS NULL AND master_card_id NOT IN 
        (SELECT master_card_id  
        FROM user_card where user_id =:userId)";
      
    }elseif(!empty($iosVer)){
      $sql = "SELECT *
      FROM master_card
      WHERE ios_version_id IS NULL AND master_card_id NOT IN 
        (SELECT master_card_id  
        FROM user_card where user_id =:userId)";
    }else{
      $sql = "SELECT *
              FROM master_card
              WHERE is_available!=0 AND master_card_id NOT IN 
                (SELECT master_card_id  
                FROM user_card where user_id =:userId)";
    }
    $result = database::doSelect($sql,array('userId' => $userId));
    return $result;
  } 
  

  public function getLockedMasterCardList($userId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE is_available!=0 AND master_card_id NOT IN 
              (SELECT master_card_id  
              FROM user_card where user_id =:userId)";

    $result = database::doSelect($sql,array('userId' => $userId));
    return $result;
  }
  public function getLockedMasterCardListWithVersion($userId,$andVer,$iosVer, $options=array())
  {
    if(!empty($andVer)){
      $sql = "SELECT *
              FROM master_card
              WHERE INET_ATON(android_version_id)<=INET_ATON('".$andVer."') AND master_card_id NOT IN 
                (SELECT master_card_id  
                FROM user_card where user_id =:userId)";
      
    }elseif(!empty($iosVer)){
      $sql = "SELECT *
              FROM master_card
              WHERE INET_ATON(ios_version_id)<=INET_ATON('".$iosVer."') AND master_card_id NOT IN 
                (SELECT master_card_id  
                FROM user_card where user_id =:userId)";
    }else{
      $sql = "SELECT *
              FROM master_card
              WHERE is_available!=0 AND master_card_id NOT IN 
                (SELECT master_card_id  
                FROM user_card where user_id =:userId)";
    }

   

    $result = database::doSelect($sql,array('userId' => $userId));
    return $result;
  }
  public function getLockedMasterCardListIdWithStadium($userId, $masterStId, $options=array())
  {
    $sql = "SELECT *
            FROM master_card
            WHERE master_stadium_id<=:masterStId AND is_available!=0  AND master_card_id NOT IN 
              (SELECT master_card_id  
              FROM user_card where user_id =:userId)";

    $result = database::doSelect($sql,array('userId' => $userId, 'masterStId'=>$masterStId ));
    return $result;
  }
  
  

  public function cardUnlock($userId, $masterStadiumId, $options=array())
  {
    $unlockingCardList = $this->getMasterCardListForUnlocking($masterStadiumId);
    //print_log("---------------------------------Unlocked List-------------------------------------------------");
    //print_log($unlockingCardList);
    //print_log("--------------------------------End Unlocked List-----------------------------------------------");
    $cardCount=1; 
    foreach($unlockingCardList as $defaultCard)
    {
      $userCard = $this->getUserCardDetailForMasterCardId($userId, $defaultCard['master_card_id']);
      //print_log("---------------------------------Card Data List-------------------------------------------------");
      //print_log($defaultCard['master_card_id']);
      //print_log($userCard);
      //print_log("--------------------------------End Card Data List----------------------------------------------");
      $userCard = $this->getUserCardForUserIdAndMasterCardId($userId, $defaultCard['master_card_id']);
      $userCardLevel = $this->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($defaultCard['master_card_id']);
      $cardLevelId=(empty($userCardLevel['level_id']))?DEFAULT_CARD_LEVEL_ID:$userCardLevel["level_id"];
      //$cardLevelId = (empty($userCard['level_id']))?DEFAULT_CARD_LEVEL_ID:$userCard["level_id"];
      //print_log($cardLevelId);
      if(empty($userCard['user_card_count'])){
        $userCardCount = (empty($cardCount))?DEFAULT_CARD_COUNT:$cardCount;
      }else{
        $userCardCount = $cardCount+$userCard['user_card_count'];
      }
      if(empty($userCard))
      {
        $userCardId = $this->insertUserCard(array(
                      'user_id' => $userId,
                      'master_card_id' => $defaultCard['master_card_id'],
                      'is_deck' => CONTENT_INACTIVE,
                      'level_id' => $cardLevelId,
                      'user_card_count' => $userCardCount,
                      'created_at' => date('Y-m-d H:i:s'),
                      'status' => CONTENT_ACTIVE ));
    
        $cardPropertyList = $this->getMasterCardPropertyList($defaultCard['master_card_id']);
        //print_log("--------------------------property-----------------------------------------------");
        //print_log($cardPropertyList);
        //print_log("------------------------End property-----------------------------------------------");
        foreach($cardPropertyList as $cardProperty)
        {
          $cardPropertyValue = $this->getCardPropertyValue($defaultCard['master_card_id'], $cardLevelId, $cardProperty['card_property_id']);
          $this->insertUserCardProperty(array(
                          'user_id' => $userId,
                          'card_property_id' => $cardProperty['card_property_id'],
                          'user_card_id' => $userCardId,
                          'user_card_property_value' => $cardPropertyValue['card_property_value'],
                          'created_at' => date('Y-m-d H:i:s'),
                          'status' => CONTENT_ACTIVE));
        }
      }
    }
  }
  public function cardUnlockWithVersion($userId, $masterStadiumId, $androidVerId, $iosVerId, $options=array())
  {
    $unlockingCardList = $this->getMasterCardListForUnlockingWithVersion($masterStadiumId, $androidVerId, $iosVerId);
    //print_log("---------------------------------Unlocked List-------------------------------------------------");
    //print_log($unlockingCardList);
    //print_log("--------------------------------End Unlocked List-----------------------------------------------");
    $cardCount=1; 
    foreach($unlockingCardList as $defaultCard)
    {
      /*if(!empty($defaultCard['android_version_id']) && !empty($androidVerId)){  
        if(version_compare($defaultCard['android_version_id'],$androidVerId, '<=')){*/
          $userCard = $this->getUserCardDetailForMasterCardId($userId, $defaultCard['master_card_id']);
          print_log("---------------------------------Card Data List-------------------------------------------------");
          print_log($defaultCard['master_card_id']);
          //print_log($userCard);
          print_log("--------------------------------End Card Data List----------------------------------------------");
          $userCard = $this->getUserCardForUserIdAndMasterCardId($userId, $defaultCard['master_card_id']);
          $userCardLevel = $this->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($defaultCard['master_card_id']);
          $cardLevelId=(empty($userCardLevel['level_id']))?DEFAULT_CARD_LEVEL_ID:$userCardLevel["level_id"];
          //$cardLevelId = (empty($userCard['level_id']))?DEFAULT_CARD_LEVEL_ID:$userCard["level_id"];
          //print_log($cardLevelId);
          if(empty($userCard['user_card_count'])){
            $userCardCount = (empty($cardCount))?DEFAULT_CARD_COUNT:$cardCount;
          }else{
            $userCardCount = $cardCount+$userCard['user_card_count'];
          }
          if(empty($userCard))
          {
            $userCardId = $this->insertUserCard(array(
                          'user_id' => $userId,
                          'master_card_id' => $defaultCard['master_card_id'],
                          'is_deck' => CONTENT_INACTIVE,
                          'level_id' => $cardLevelId,
                          'user_card_count' => $userCardCount,
                          'created_at' => date('Y-m-d H:i:s'),
                          'status' => CONTENT_ACTIVE ));
        
            $cardPropertyList = $this->getMasterCardPropertyList($defaultCard['master_card_id']);
            //print_log("--------------------------property-----------------------------------------------");
            //print_log($cardPropertyList);
            //print_log("------------------------End property-----------------------------------------------");
            foreach($cardPropertyList as $cardProperty)
            {
              $cardPropertyValue = $this->getCardPropertyValue($defaultCard['master_card_id'], $cardLevelId, $cardProperty['card_property_id']);
              $this->insertUserCardProperty(array(
                              'user_id' => $userId,
                              'card_property_id' => $cardProperty['card_property_id'],
                              'user_card_id' => $userCardId,
                              'user_card_property_value' => $cardPropertyValue['card_property_value'],
                              'created_at' => date('Y-m-d H:i:s'),
                              'status' => CONTENT_ACTIVE));
            }
          }
       /* }
      }*/
      /*
      if(!empty($defaultCard['ios_version_id']) && !empty($iosVerId)){
          if(version_compare($defaultCard['ios_version_id'],$iosVerId, '<=')){
          $userCard = $this->getUserCardDetailForMasterCardId($userId, $defaultCard['master_card_id']);
          //print_log("---------------------------------Card Data List-------------------------------------------------");
          //print_log($defaultCard['master_card_id']);
          //print_log($userCard);
          //print_log("--------------------------------End Card Data List----------------------------------------------");
          $userCard = $this->getUserCardForUserIdAndMasterCardId($userId, $defaultCard['master_card_id']);
          $userCardLevel = $this->getUserCardUnlockLevelOnRarityTypeAndMasterCardId($defaultCard['master_card_id']);
          $cardLevelId=(empty($userCardLevel['level_id']))?DEFAULT_CARD_LEVEL_ID:$userCardLevel["level_id"];
          //$cardLevelId = (empty($userCard['level_id']))?DEFAULT_CARD_LEVEL_ID:$userCard["level_id"];
          //print_log($cardLevelId);
          if(empty($userCard['user_card_count'])){
            $userCardCount = (empty($cardCount))?DEFAULT_CARD_COUNT:$cardCount;
          }else{
            $userCardCount = $cardCount+$userCard['user_card_count'];
          }
          if(empty($userCard))
          {
            $userCardId = $this->insertUserCard(array(
                          'user_id' => $userId,
                          'master_card_id' => $defaultCard['master_card_id'],
                          'is_deck' => CONTENT_INACTIVE,
                          'level_id' => $cardLevelId,
                          'user_card_count' => $userCardCount,
                          'created_at' => date('Y-m-d H:i:s'),
                          'status' => CONTENT_ACTIVE ));
        
            $cardPropertyList = $this->getMasterCardPropertyList($defaultCard['master_card_id']);
            //print_log("--------------------------property-----------------------------------------------");
            //print_log($cardPropertyList);
            //print_log("------------------------End property-----------------------------------------------");
            foreach($cardPropertyList as $cardProperty)
            {
              $cardPropertyValue = $this->getCardPropertyValue($defaultCard['master_card_id'], $cardLevelId, $cardProperty['card_property_id']);
              $this->insertUserCardProperty(array(
                              'user_id' => $userId,
                              'card_property_id' => $cardProperty['card_property_id'],
                              'user_card_id' => $userCardId,
                              'user_card_property_value' => $cardPropertyValue['card_property_value'],
                              'created_at' => date('Y-m-d H:i:s'),
                              'status' => CONTENT_ACTIVE));
            }
          }
        }
      }*/
    }
  }

  public function getUserCardForCurrentDeck($userId, $isDeck, $cadId, $options=array())
  {
    $sql = "SELECT uc.*, mc.title, mc.card_type, mc.is_available, mc.card_description, mc.card_rarity_type,mc.bundlename,mc.android_bundlehash,mc.android_bundlecrc,mc.ios_bundlehash,mc.ios_bundlecrc
            FROM user_card uc
            INNER JOIN master_card mc ON uc.master_card_id = mc.master_card_id
            WHERE uc.user_id = :userId AND uc.is_deck = :isDeck AND uc.master_card_id IN (". $cadId .")
            ORDER BY uc.user_card_id";

    $result = database::doSelect($sql, array('userId' => $userId, 'isDeck' => $isDeck));
    return $result;
  }
  
  public function getPlanetsList($userId, $options=array())
  {
    $sql = "SELECT *
            FROM master_planets";
    $result = database::doSelect($sql);
    return $result;
  }
  public function getCharactersList($userId, $options=array())
  {
    $sql = "SELECT *, card_id AS characterID, meterial_id as materialid
            FROM planet_characters";
    $result = database::doSelect($sql);
    return $result;
  }
  public function getCharacterUnlockedList($userId){
    $sql = "SELECT c.card_id, c.character_name, c.description, c.buy_type, c.amount, c.unlock_level, c.meterial_id, c.position, c.rotation,c.status, c.planet_no,c.bundle_status,ci.status,
    (CASE WHEN c.status THEN ci.status 
      ELSE 'no status'
        END ) AS STATUSVALUE
    FROM planet_characters AS c
    LEFT JOIN character_inventory AS ci 
    ON c.card_id=ci.card_id
    WHERE ci.user_id = :userId";
    $result = database::doSelect($sql, array('userId' => $userId));
    return $result;
  }

  public function getCharacterDetails($cardId){
    $sql = "SELECT *, card_id as characterID
            FROM planet_characters
            WHERE card_id=:cardId";

    $result = database::doSelectOne($sql, array('cardId' => $cardId));
    return $result;
  }
  public function checkCharacterDetails($cardId, $userId){
    $sql = "SELECT *
            FROM character_inventory
            WHERE card_id = :cardId AND user_id= :userId";

    $result = database::doSelectOne($sql, array('cardId' => $cardId, 'userId' => $userId));
    return $result; 
  }
  public function unlockArCharacter($options=array()){
    $sql = "INSERT INTO character_inventory";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
}
