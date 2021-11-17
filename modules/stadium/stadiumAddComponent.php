<?php
/**
 * Author : Abhijth Shetty
 * Date   : 12-02-2018
 * Desc   : This is a controller file for stadiumAdd Component
 */
class stadiumAddComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->card = $this->stadium = array();
    $masterLib = autoload::loadLibrary('queryLib', 'master');

    $this->stadium = $masterLib->getStadiumDetail($_GET['stadiumId']);

    if(isPost())
    {
      $mandatoryFields = array('title' => $_POST['title'], 'relics_count_min' => $_POST['relics_count_min'],
                               'relics_count_max' => $_POST['relics_count_max']);

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
      $data['relics_count_min'] = trim($_POST['relics_count_min']);
      $data['relics_count_max'] = trim($_POST['relics_count_max']);

      if(empty($_GET['stadiumId']))
      {
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');

        $masterLib->insertMasterStadium($data);
        $this->redirectTo(getComponentUrl('stadium', 'listStadium'));
      }

      if(!empty($_GET['stadiumId']))
      {
        $masterLib->updateMasterStadium($_GET['stadiumId'],$data);
        $this->redirectTo(getComponentUrl('stadium', 'listStadium'));
      }
    }
  }
}
