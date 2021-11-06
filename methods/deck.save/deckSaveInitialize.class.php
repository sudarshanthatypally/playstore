<?php
class deckSaveInitialize extends baseInitialize{

  public $requestMethod = array("GET", "POST");
  public $isSecured = true;
	
  public function getParameter()
  {
    $parameter = array();
    
    $parameter["deckData"] = array(
      "name"=>"deck_data",
      "type"=>"text",
      "required"=>true,
      "default"=>"",
      "description"=>'{"current_deck_number": "0","deck_details":[{"deck_id":0,"cards":[{"master_id":1},{"master_id":2},{"master_id":3},{"master_id":4},{"master_id":5},{"master_id":6},{"master_id":7},{"master_id":8}]},{"deck_id":1,"cards":[{"master_id":11},{"master_id":12},{"master_id":13},{"master_id":14},{"master_id":5},{"master_id":16},{"master_id":7},{"master_id":18}]},{"deck_id":1,"cards":[{"master_id":11},{"master_id":12},{"master_id":13},{"master_id":14},{"master_id":5},{"master_id":16},{"master_id":7},{"master_id":18}]},{"deck_id":1,"cards":[{"master_id":11},{"master_id":12},{"master_id":13},{"master_id":14},{"master_id":5},{"master_id":16},{"master_id":7},{"master_id":18}]}]}'
    );
    
    return $parameter;
  }
}