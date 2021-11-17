<?php
/**
 * Author : Abhijth Shetty
 * Date   : 12-04-2019
 * Desc   : This is a controller file for badgeAdd Component
 */
class badgeAddComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->badge = array();

    $badgeLib = autoload::loadLibrary('queryLib', 'badge');
    $this->badge = $badgeLib->getMasterBadgeDetail($_GET['masterBadgeId']);

    if(isPost())
    {
      $mandatoryFields = array('min_relic_count' => $_POST['min_relic_count'],
                               'max_relic_count' => $_POST['max_relic_count']);

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
      $data['min_relic_count'] = trim($_POST['min_relic_count']);
      $data['max_relic_count'] = trim($_POST['max_relic_count']);

      if(empty($_GET['masterBadgeId']))
      {
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');
        $badgeLib->insertMasterBadge($data);
      }

      if(!empty($_GET['masterBadgeId']))
      {
        $badgeLib->updateMasterbadge($_GET['masterBadgeId'], $data);
      }
      $this->redirectTo(getComponentUrl('badge', 'list'));

    }
  }
}
