<?php
/**
 * Author : Abhijth Shetty
 * Date   : 10-02-2018
 * Desc   : This is a controller file for achievementEdit Component
 */
class achievementEditComponent extends baseComponent
{
  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js,jquery.dataTables.min.js,jquery.dataTables.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css,jquery.dataTables.min.css,jquery.dataTables_themeroller.css");
    $this->achievement = array();

    $achievementLib = autoload::loadLibrary('queryLib', 'achievement');
    $this->achievement = $achievementLib->getMasterAchievementDetail($_GET['masterAchievementId']);

    if(isPost())
    {
      $mandatoryFields = array('title' => $_POST['title'], 'description' => $_POST['description'],
                               'achievement_type' => $_POST['achievement_type'], 'xp' => $_POST['xp']);

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
      $data['description'] = trim($_POST['description']);
      $data['achievement_type'] = trim($_POST['achievement_type']);
      $data['count'] = trim($_POST['count']);
      $data['cube_id'] = trim($_POST['cube_id']);
      $data['xp'] = trim($_POST['xp']);

      if(empty($_GET['masterAchievementId']))
      {
        $data['status'] = CONTENT_ACTIVE;
        $data['created_at'] = date('Y-m-d H:i:s');
        $achievementLib->insertMasterAchievement($data);
        $this->redirectTo(getComponentUrl('achievement', 'list'));
      }

      if(!empty($_GET['masterAchievementId']))
      {
        $achievementLib->updateMasterAchievement($_GET['masterAchievementId'], $data);
        $this->redirectTo(getComponentUrl('achievement', 'list'));
      }
    }
  }
}
