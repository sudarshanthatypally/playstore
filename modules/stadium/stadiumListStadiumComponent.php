<?php
/**
 * Author : Abhijth Shetty
 * Date   : 18-01-2018
 * Desc   : This is a controller file for stadiumListStadium Component
 */
class stadiumListStadiumComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->stadiumList = array();

    $masterLib = autoload::loadLibrary('queryLib', 'master');
    $this->stadiumList = $masterLib->getMasterStadiumList();

    

  }
}
