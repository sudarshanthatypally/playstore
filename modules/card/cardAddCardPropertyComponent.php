<?php
/**
 * Author : Abhijth Shetty
 * Date   : 13-02-2018
 * Desc   : This is a controller file for cardAddCardProperty Component
 */
class cardAddCardPropertyComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->cardMasterPropertyList = $this->cardProperty = $data = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $this->cardProperty = $masterLib->getMasterCardPropertyDetail($_GET['cardPropertyId']);
    $this->cardMasterPropertyList = $masterLib->getCardMasterPropertyList();

    if(isPost())
    {
      $mandatoryFields = array('property_id' => $_POST['property_id'], 'card_property_value' => $_POST['card_property_value']);

      foreach($mandatoryFields as $field => $value )
      {
        if(isset($_POST[$field]) && $_POST[$field] == "" )
        {
          $this->result['status'] = true;
          $this->result['message'] = str_replace('_',' ',$field)." is mandatory";
          return false;
        }
      }

      $data['property_id'] = trim($_POST['property_id']);
      //$data['card_property_value'] = trim($_POST['card_property_value']);
      $data['is_default'] = trim($_POST['is_default']);

      foreach ($this->cardMasterPropertyList as $cardMasterProperty)
      {
        if($data['property_id'] == $cardMasterProperty['master_property_id']){
          $data['property_name'] = $cardMasterProperty['master_property_name'];
        }
      }

      if(empty($_GET['cardPropertyId']))
      {
        $cardProperty  = $masterLib->checkMasterCardPropertyExist($_GET['masterCardId'], $data['property_id']);

        if(!empty($cardProperty)){
          $this->redirectTo(getComponentUrl('card', 'listCardProperty', array('masterCardId' => $_GET['masterCardId'])));
        }
        $data['master_card_id'] = $_GET['masterCardId'];
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->NewCard = $masterLib->insertCardProperty($data);
        $this->redirectTo(getComponentUrl('card', 'listCardProperty', array('masterCardId' => $_GET['masterCardId'])));
      }

      if(!empty($_GET['cardPropertyId']))
      {
        $masterLib->updateCardProperty($_GET['cardPropertyId'], $data);
        $this->redirectTo(getComponentUrl('card', 'listCardProperty', array('masterCardId' => $_GET['masterCardId'])));
      }
    }
  }
}
