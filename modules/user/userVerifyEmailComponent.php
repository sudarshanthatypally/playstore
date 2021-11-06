<?php
/**
 * Author : Abhijth Shetty
 * Date   : 05-02-2016
 * Desc   : This is a controller file for userVerifyEmail Component
 */
class userVerifyEmailComponent extends baseComponent
{
  public $isSecured = false;
  public $layoutName = "";
  public function execute()
  {
    $this->includeJavascript('bootstrap.min.js,jquery-1.11.1.min.js,jquery.dataTables.min.js');
    $this->includeStylesheet("bootstrap.min.css,admin.css,jquery.dataTables.css");

    $userTempLib = autoload::loadLibrary('queryLib', 'userTempSession');
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $this->result = array('status'=>false, 'message'=>"");


    $session = $userTempLib->getUserTempSessionDetailForSession($_GET['sessionId']);


    if($session['type'] != SESSION_TYPE_USER_REGISTRATION)
    {
      $this->result['status'] = true;
      $this->result['message'] = "Invalid session";
      return false;
    }

    if($session['status'] == CONTENT_INACTIVE)
    {
      $this->result['status'] = true;
      $this->result['message'] = "Oops! Looks like the link has expired.<br/>";
      return false;
    }

    $user = $userLib->getUserDetail($session['user_id']);

    if($session['type'] == SESSION_TYPE_USER_REGISTRATION  && $session['status'] == CONTENT_ACTIVE)
    {
      $userTempLib->updateUserTempSession($session['user_temp_session_id'], array('status' => CONTENT_INACTIVE));
      $userLib->updateUser($session['user_id'], array('email_id' => $user['temp_email_id'],'temp_email_id' =>"", 'status' => CONTENT_ACTIVE));
      $this->result['status'] = true;
      $this->result['message'] = "Congrats! You have been successfully registered for Fun Run. We hope you have a pleasant experience.<br/><br/>Thanks, <br/>Fun Run team";
    }
  }
}
