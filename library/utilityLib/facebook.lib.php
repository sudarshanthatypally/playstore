<?php
class facebook
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

  public function getFacebookIdFromAccessToken($facebookaccessToken)
  {
  	$url ="https://graph.facebook.com/me?fields=id&access_token=".$facebookaccessToken;
        $curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_URL, $url);
	$response = curl_exec($curl);
	curl_close($curl);
      $data = json_decode($response, true);
	return isset($data['id'])?$data['id']:0;
  }

  
  public function parse_signed_request($signed_request) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);
  
    $secret = "8fd534c7ac1180efd06a59ccf6f1f68b"; // Use your app secret here
  
    // decode the data
    $sig = $this->base64_url_decode($encoded_sig);
    $data = json_decode($this->base64_url_decode($payload), true);
  
    // confirm the signature
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) {
      error_log('Bad Signed JSON signature!');
      return null;
    }
  
    return $data;
  }

  public function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
  }
}
?>
