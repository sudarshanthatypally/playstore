<?php
/**
 * Author : Abhijth Shetty
 * Date   : 16-03-2018
 * Desc   : This is a controller file for achievementList Action
 */
class logsSaveAction extends baseAction{
	
  public function execute()
  {
    //$achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    //$result = array();
    date_default_timezone_set("Asia/Kolkata");

    serverLog($this->apiName, $this->logMessage, $this->errorLevel);

    $this->setResponse('SUCCESS');
    return array();
  }
}
