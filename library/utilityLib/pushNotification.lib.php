<?php
class pushNotification
{
  protected static $objInstance;

  public static function get()
  {
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function sendPushNotification($userId, $message, $options=array())
  {
    $userLib = autoload::loadLibrary('queryLib', 'user');
    $userDetail = $userLib->getUserDetail($userId);
    $result = array();

    if($userDetail['android_push_token'] !="")
    {
      $result['android'] = $this->androidPushNotification($userDetail['android_push_token'], $message, $options);
    }

    // if ($userDetail['ios_push_token'] !="")
    // {
    //   $result['ios'] = $this->iosFcmPushNotification($userDetail['ios_push_token'], $message, $options);
    // }

    if ($userDetail['ios_push_token'] !="")
    {
      //development
      if(!empty(getConfig('ios_push_certificate_dev')))
      {
        $options = array('apns_url' => getConfig('ios_push_url_dev'), 'apns_cert' => getConfig('ios_push_certificate_dev'), 'passphrase' => getConfig('ios_push_passphrase_dev'));
        $result['ios'] = $this->iOSPushNotification($userDetail['ios_push_token'], $message, $options);
      }

      //adhoc
      if(!empty(getConfig('ios_push_certificate_adhoc')))
      {
        $options = array( 'apns_url' => getConfig('ios_push_url_adhoc'), 'apns_cert' => getConfig('ios_push_certificate_adhoc'), 'passphrase' => getConfig('ios_push_passphrase_adhoc'));
        $result['ios'] = $this->iOSPushNotification($userDetail['ios_push_token'], $message, $options);
      }
    }

    return $result;
  }

  public function androidPushNotification($deviceToken, $message, $options=array())
  {
   // prep the bundle
    $msg = array
    (
      'body' => $message,
      'title'	=> '',
      'icon' => '@drawable/pw_notification',
      'sound' => 'mySound'
    );

    $fields = array
    (
      'to' => $deviceToken,
      'notification' => $msg,
	  //'data' => $options['data']
    );

    $headers = array
    (
      'Authorization: key='. getConfig('push_notification_legacy_key'),
      'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields));
    $result = curl_exec($ch );
    curl_close( $ch );

    return json_decode($result, true);
  }

  public function iosFcmPushNotification($deviceToken, $message, $options=array())
  {
   // prep the bundle
    $msg = array
    (
      'body' => $message,
      'title'	=> '',
      'icon' => 'myicon',
      'sound' => 'mySound'
    );

    $fields = array
    (
      'to' => $deviceToken,
      'notification' => $msg,
	  //'data' => $options['data']
    );

    $headers = array
    (
      'Authorization: key='. getConfig('push_notification_legacy_key'),
      'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields));
    $result = curl_exec($ch );
    curl_close( $ch );

    return json_decode($result, true);
  }


  public function iOSPushNotification($deviceToken, $message, $options=array())
  {
    $badge = 1;
    $sound = 'default';
    $apns_port = 2195;
    $apns_url  = $options['apns_url'];
    $apns_cert = $options['apns_cert'];
    $passphrase = $options['passphrase'];

    $payload = array();
    $payload['aps'] = array('alert' => $message, 'badge' => intval($badge), 'sound' => $sound, 'data' => $options['data']);
    $payload = json_encode($payload);

    $stream_context = stream_context_create();
    stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
    stream_context_set_option($stream_context, 'ssl', 'passphrase', $passphrase);

    $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
    //log::show($error);
    //log::show($error_string);
    $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    $res = fwrite($apns, $apns_message, strlen($apns_message));

    //log::show($res);
    @socket_close($apns);
    @fclose($apns);
    return $res;
  }
}
