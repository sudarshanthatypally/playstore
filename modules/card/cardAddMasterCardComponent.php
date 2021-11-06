<?php
/**
 * Author : Abhijth Shetty
 * Date   : 13-02-2018
 * Desc   : This is a controller file for cardAddMasterCard Component
 */
class cardAddMasterCardComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->card = $this->stadium = $this->stadiumList = $temp = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $cardLib = autoload::loadLibrary('queryLib', 'card');

    $this->card = $cardLib->getMasterCardDetail($_GET['masterCardId']);
    $stadium = $masterLib->getStadiumDetail($this->card["master_stadium_id"]);
    $this->card['stadium_title'] =  $stadium['title'];

    $this->stadiumList = $masterLib->getMasterStadiumList();

    if(isPost())
    {
      $mandatoryFields = array('title' => $_POST['title'], 'card_max_level' => $_POST['card_max_level'],
                               'card_description' => $_POST['card_description']);

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
      $data['title'] = trim($_POST['title']);
      $data['master_stadium_id'] = trim($_POST['master_stadium_id']);
      $data['card_type'] = trim($_POST['card_type']);
      $data['card_rarity_type'] = trim($_POST['rarity_type']);
      $data['is_card_default'] = trim($_POST['is_card_default']);
      $data['card_max_level'] = trim($_POST['card_max_level']);
      $data['card_description'] = trim($_POST['card_description']);

      if(empty($_GET['masterCardId']))
      {
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');
        $cardLib->insertMasterCard($data);
        $this->redirectTo(getComponentUrl('card', 'listMasterCard'));
      }

      if(!empty($_GET['masterCardId']))
      {
        $cardLib->updateMasterCard($_GET['masterCardId'], $data);
        $this->redirectTo(getComponentUrl('card', 'listMasterCard'));
      }
    }
  }
}
