<?php
/**
 * Author : Abhijth Shetty
 * Date   : 03-02-2018
 * Desc   : This is a controller file for cardDeleteCardProperty Component
 */
class cardDeleteCardPropertyComponent extends baseComponent
{
  public function execute()
  {
    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $this->card = $masterLib->getMasterCardPropertyDetail($_GET['cardPropertyId']);

    if(isset($_GET['cardPropertyId']) && $_GET['cardPropertyId'] > 0){
      $masterLib->deleteCardProperty($_GET['cardPropertyId']);
    }

    $this->redirectTo(getComponentUrl('card', 'listCardProperty', array('masterCardId'=>$this->card['master_card_id'])));
  }
}
