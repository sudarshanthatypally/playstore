<?php
/**
 * Author : Abhijth Shetty
 * Date   : 04-02-2016
 * Desc   : This is a controller file for userResetPassword Component
 */
class userResetPasswordComponent extends baseComponent
{
  public $isSecured = false;

  public $layoutName = "";

  public function execute()
  {
    $this->includeJavascript('jquery.js,bootstrap.min.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,style.css");

    $userTempLib = autoload::loadLibrary('queryLib', 'userTempSession');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $this->result = array('status'=>false, 'message'=>"");

    $session = $userTempLib->getSessionDetailForSession($_GET['sessionId']);
    if($session['type'] != SESSION_TYPE_PASSWORD_RESET)
    {
      $this->result['status'] = 1;
      $this->result['message'] = "Invalid session";
      return false;
    }

    //if session id (created_at) greater than 24 hours is not valid
    if($session['status'] == CONTENT_INACTIVE || (time() >= (strtotime($session['created_at'])  + 86400)))
    {
      $this->result['status'] = 1;
      $this->result['message'] = "Oh! Looks like the session has expired.<br/><br/> Thanks, <br/>Fun Run Team";
      return false;
    }

    $userDetail = $userLib->getUserDetail($session['user_id']);
    $this->userName = $userDetail['user_name'];
    if(isPost())
    {
      if(empty($_POST['new_password']))
      {
        $this->result['status'] = 1;
        $this->result['message'] = "Please enter password";
        return false;
      }

      if($_POST['new_password'] != $_POST['retype_new_password'])
      {
        $this->result['status'] = 1;
        $this->result['message'] = "Oops! Please enter valid content in the given fields.<br/><br/> Thanks, <br/>Fun Run Team";
        return false;
      }

      $password = $_POST["new_password"];

      $userTempLib->updateUserTempSession($session['user_temp_session_id'], array('status' => CONTENT_INACTIVE));
      $userLib->updateUser($session['user_id'], array('password' => password_hash($password,PASSWORD_BCRYPT)));

      $this->result['status'] = 2;
      $this->result['message'] = "Congrats, your password has been successfully reset. Please login using your new password.<br/><br/> Thanks, <br/>Fun Run Team";
    }
  }
}
