<?php
/**
 * Author : Abhijth Shetty
 * Date   : 10-02-2018
 * Desc   : This is a controller file for cardListCardPropertyLevel Component
 */
class cardListCardPropertyLevelComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->cardPropertyLevelList = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $cardLib = autoload::loadLibrary('queryLib', 'card');
    $cardPropertyLevelList = $masterLib->getCardPropertyLevelList($_GET['cardPropertyId'], $_GET['masterCardId']);

    foreach ($cardPropertyLevelList as $propertyLevel)
    {
      $temp = array();
      $temp['card_property_level_upgrade_id'] = $propertyLevel['card_property_level_upgrade_id'];
      $temp['card_property_id'] = $propertyLevel['card_property_id'];
      $temp['master_card_id'] = $propertyLevel['master_card_id'];
      $temp['title'] = $propertyLevel['title'];
      $temp['level_id'] = $propertyLevel['level_id'];
      $temp['card_property_value'] = $propertyLevel['card_property_value'];
      $temp['status'] = 1;

      $this->cardPropertyLevelList[] = $temp;
    }
  }
}
