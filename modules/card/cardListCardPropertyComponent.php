<?php
/**
 * Author : Abhijth Shetty
 * Date   : 10-02-2018
 * Desc   : This is a controller file for cardListCardProperty Component
 */
class cardListCardPropertyComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->cardPropertyList = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $cardLib = autoload::loadLibrary('queryLib', 'card');

    $masterCardPropertyList = $cardLib->getMasterCardPropertyList($_GET['masterCardId']);

    foreach ($masterCardPropertyList as $cardProperty)
    {
      $temp = array();
      $temp['card_property_id'] = $cardProperty['card_property_id'];
      $temp['property_id'] = $cardProperty['property_id'];
      $temp['property_name'] = $cardProperty['property_name'];
      $temp['card_property_value'] = $cardProperty['card_property_value'];
      $temp['is_default'] = $cardProperty['is_default'];
      $temp['status'] = 1;

      $this->cardPropertyList[] = $temp;
    }
  }
}
