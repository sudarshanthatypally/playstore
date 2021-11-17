<?php
class invite{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function getInviteList($options=array())
  {
    $sql = "SELECT *
            FROM invite";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getFriendlyInviteList($options=array())
  {
    $sql = "SELECT *
            FROM friendly_invite";

    $result = database::doSelect($sql);
    return $result;
  }

  public function getFriendlyInviteDetail($inviteId, $options=array())
  {
    $sql = "SELECT *
            FROM friendly_invite
            WHERE friendly_invite_id=:inviteId"; 

    $result = database::doSelectOne($sql, array('inviteId'=>$inviteId));
    return $result;
  }
  public function getFriendlyInviteDetailByUserId($userId, $options=array())
  {
    $sql = "SELECT *
            FROM friendly_invite
            WHERE user_id=:userId
            ORDER BY friendly_invite_id DESC, created_at DESC
            LIMIT 1";

    $result = database::doSelectOne($sql, array('userId'=>$userId));
    return $result;
  }
  public function getFriendlyInviteDetailByOppId($oppId, $options=array())
  {
    $sql = "SELECT *
            FROM friendly_invite
            WHERE user_id=:oppId
            ORDER BY friendly_invite_id DESC, created_at DESC
            LIMIT 1";

    $result = database::doSelectOne($sql, array('oppId'=>$oppId));
    return $result;
  }

  public function getInviteDetail($inviteId, $options=array())
  {
    $sql = "SELECT *
            FROM invite
            WHERE invite_id=:inviteId";

    $result = database::doSelectOne($sql, array('inviteId'=>$inviteId));
    return $result;
  }

  public function insertInvite($options=array())
  {
    $sql = "INSERT INTO invite ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }

  public function insertFriendlyInvite($options=array())
  {
    $sql = "INSERT INTO friendly_invite ";
    $sql .= "( ".implode(", ", array_keys($options))." ) VALUES ";
    $sql .= "( :".implode(", :", array_keys($options))." )";

    $result = database::doInsert($sql, $options);
    return $result;
  }
  public function updateInvite($inviteId, $options=array())
  {
    $sql = "UPDATE invite SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE invite_id =:inviteId";
    $options['inviteId'] = $inviteId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function updateBattleInvite($inviteId, $options=array())
  {
    $sql = "UPDATE friendly_invite SET ";
    foreach($options as $key=>$value){
      $sql .= $key."= :".$key.", ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE friendly_invite_id =:inviteId";
    $options['inviteId'] = $inviteId;

    $result = database::doUpdate($sql, $options);
    return $result;
  }
  public function deleteInvite($inviteId, $options=array())
  {
    $sql = "DELETE FROM invite
            WHERE invite_id = :inviteId";

	  $result = database::doDelete($sql, array('inviteId'=>$inviteId));
    return $result;
  }

  public function getActiveInviteDetailForInviteToken($inviteToken, $options=array())
  {
    $sql = "SELECT *
            FROM invite
            WHERE invite_token=:inviteToken
            AND status<>".CONTENT_ACCEPTED;

    $result = database::doSelectOne($sql, array('inviteToken'=>$inviteToken));
    return $result;
  }

  public function getActiveBattleInviteDetailForInviteToken($inviteToken, $options=array())
  {
    $sql = "SELECT *
            FROM friendly_invite
            WHERE invite_token=:inviteToken"; 
           // AND status<>".CONTENT_ACCEPTED;

    $result = database::doSelectOne($sql, array('inviteToken'=>$inviteToken));
    return $result;
  }

  public function getInviteListWithLimit($userId, $limit, $options=array())
  {
    $sql = "SELECT *
            FROM invite
            WHERE user_id=:userId
            ORDER BY invite_id DESC
            LIMIT ".$limit;

    $result = database::doSelect($sql, array('userId'=>$userId));
    return $result;
  }
  public function getFriendlyInviteListWithLimit($userId, $limit, $options=array())
  {
    $sql = "SELECT *
            FROM friendly_invite
            WHERE user_id=:userId
            ORDER BY friendly_invite_id DESC
            LIMIT ".$limit;

    $result = database::doSelect($sql, array('userId'=>$userId));
    return $result;
  }
}