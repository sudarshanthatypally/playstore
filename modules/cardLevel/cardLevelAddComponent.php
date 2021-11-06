<?php
/**
 * Author : Abhijth Shetty
 * Date   : 12-02-2018
 * Desc   : This is a controller file for cardLevelAdd Component
 */
class cardLevelAddComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->userLevel =  $temp = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');

    $this->cardLevel = $masterLib->getMasterCardLevelUpgradeDetail($_GET['masterCardLevelUpgradeId']);

    if(isPost())
    {
      $mandatoryFields = array('level_id' => $_POST['level_id'], 'xp' => $_POST['xp'],
                               'gold' => $_POST['gold'], 'card_count' => $_POST['card_count']);

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
      $data['rarity_type'] = trim($_POST['rarity_type']);
      $data['xp'] = trim($_POST['xp']);
      $data['gold'] = trim($_POST['gold']);
      $data['card_count'] = trim($_POST['card_count']);

      if(empty($_GET['masterCardLevelUpgradeId']))
      {
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');

        $masterLib->insertMasterCardLevelUpgrade($data);
        $this->redirectTo(getComponentUrl('cardLevel', 'listCardLevel'));
      }

      if(!empty($_GET['masterCardLevelUpgradeId']))
      {
        $masterLib->updateMasterCardLevelUpgrade($_GET['masterCardLevelUpgradeId'], $data);
        $this->redirectTo(getComponentUrl('cardLevel', 'listCardLevel'));
      }
    }
  }
}
