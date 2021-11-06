<?php
/**
 * Author : Abhijth Shetty
 * Date   : 12-02-2018
 * Desc   : This is a controller file for userLevelAdd Component
 */
class userLevelAddComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->userLevel =  $temp = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');

    $this->userLevel = $masterLib->getMasterLevelUpDetail($_GET['masterLevelUpId']);

    if(isPost())
    {
      $mandatoryFields = array('level_id' => $_POST['level_id'], 'xp_to_next_level' => $_POST['xp_to_next_level'],
                               'god_tower_health' => $_POST['god_tower_health'], 'god_tower_damage' => $_POST['god_tower_damage'],
                               'stadium_tower_health' => $_POST['stadium_tower_health'], 'stadium_tower_damage' => $_POST['stadium_tower_damage']);

      foreach($mandatoryFields as $field => $value )
      {
        if(isset($_POST[$field]) && $_POST[$field] == "" )
        {
          $this->result['status'] = true;
          $this->result['message'] = str_replace('_',' ',$field)." is mandatory";
          return false;
        }
      }

      $data = array();
      $data['level_id'] = trim($_POST['level_id']);
      $data['xp_to_next_level'] = trim($_POST['xp_to_next_level']);
      $data['god_tower_health'] = trim($_POST['god_tower_health']);
      $data['god_tower_damage'] = trim($_POST['god_tower_damage']);
      $data['stadium_tower_health'] = trim($_POST['stadium_tower_health']);
      $data['stadium_tower_damage'] = trim($_POST['stadium_tower_damage']);

      if(empty($_GET['masterLevelUpId']))
      {
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');

        $masterLib->insertMasterLevelUp($data);
        $this->redirectTo(getComponentUrl('userLevel', 'listUserLevel'));
      }

      if(!empty($_GET['masterLevelUpId']))
      {
        $masterLib->updateMasterLevelUp($_GET['masterLevelUpId'],$data);
        $this->redirectTo(getComponentUrl('userLevel', 'listUserLevel'));
      }
    }
  }
}
