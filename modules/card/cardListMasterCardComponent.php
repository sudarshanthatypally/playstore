<?php
/**
 * Author : Abhijth Shetty
 * Date   : 17-01-2018
 * Desc   : This is a controller file for cardListMasterCard Component
 */
class cardListMasterCardComponent extends baseComponent
{
  public function execute()
  {
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->cardList = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');

    $cardList = $masterLib->getMasterCardList();

    foreach ($cardList as $card)
    {
      $temp = array();
      $temp['master_card_id'] = $card['master_card_id'];
      $temp['title'] = $card['title'];
      $temp['master_stadium_id'] = $card['master_stadium_id'];
      $temp['card_type'] = ($card['card_type'] == CARD_TYPE_CHARACTER)?'Character':'Power';
      $temp['rarity_type'] = ($card['card_rarity_type'] == CARD_RARITY_COMMON)?'Common':(($card['card_rarity_type'] == CARD_RARITY_RARE)?'Rare':'Ultra Rare');
      $temp['is_card_default'] = $card['is_card_default'];
      $temp['card_max_level'] = $card['card_max_level'];
      $temp['card_description'] = $card['card_description'];
      $temp['status'] = 1;

      $this->cardList[] = $temp;
    }
  }
}
