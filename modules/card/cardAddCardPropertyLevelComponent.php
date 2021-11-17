<?php
/**
 * Author : Abhijth Shetty
 * Date   : 13-02-2018
 * Desc   : This is a controller file for cardAddCardPropertyLevel Component
 */
class cardAddCardPropertyLevelComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");

    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $this->propertyLevelUp = $masterLib->getCardPropertyLevelUpgradeDetail($_GET['cardPropertyLevelUpgradeId']);

    if(isPost())
    {
      $mandatoryFields = array('level_id' => $_POST['level_id'], 'card_property_value' => $_POST['card_property_value']);

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
      $data['card_property_value'] = trim($_POST['card_property_value']);

      if(empty($_GET['cardPropertyLevelUpgradeId']))
      {
        $data['master_card_id'] = $_GET['masterCardId'];
        $data['card_property_id'] = $_GET['cardPropertyId'];
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->Newcard = $masterLib->insertCardPropertyLevelUpgrade($data);
        $this->redirectTo(getComponentUrl('card', 'listCardPropertyLevel', array('cardPropertyId' => $_GET['cardPropertyId'], 'masterCardId' => $_GET['masterCardId'])));
      }

      if(!empty($_GET['cardPropertyLevelUpgradeId']))
      {
        $data['master_card_id'] = $_GET['masterCardId'];
        $data['card_property_id'] = $_GET['cardPropertyId'];
        $masterLib->updateCardPropertyLevel($_GET['cardPropertyLevelUpgradeId'],$data);
        $this->redirectTo(getComponentUrl('card', 'listCardPropertyLevel', array('cardPropertyId' => $_GET['cardPropertyId'], 'masterCardId' => $_GET['masterCardId'])));
      }
    }
  }
}
