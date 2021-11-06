<?php
/**
 * Author : Abhijth Shetty
 * Date   : 12-07-2019
 * Desc   : This is a controller file for privacyPolicy Action 
 */
class privacyPolicyAction extends baseAction{
  /**
   * @OA\Get(path="?methodName=privacy.policy", tags={"Privacy Policy"}, 
   * @OA\Parameter(parameter="applicationKey", name="applicationKey", description="The applicationKey specific to this event",
   *   @OA\Schema(type="string"), in="query", required=false),
   * @OA\Response(response="200", description="Success, Everything worked as expected"),
   * @OA\Response(response="201", description="api_method does not exists"),
   * @OA\Response(response="202", description="The requested version does not exists"),
   * @OA\Response(response="203", description="The requested request method does not exists"),
   * @OA\Response(response="204", description="The auth token is invalid"),
   * @OA\Response(response="205", description="Response code failure"),
   * @OA\Response(response="206", description="paramName should be a Valid email address"),
   * @OA\Response(response="216", description="Invalid Credential, Please try again."),
   * @OA\Response(response="228", description="error"),
   * @OA\Response(response="231", description="Device token is mandatory."),
   * @OA\Response(response="232", description="Custom Error"),
   * @OA\Response(response="245", description="Player is offline"),
   * @OA\Response(response="404", description="Not Found")
   * )
   */
  public function execute()
  {
    //$anyLib = autoload::loadLibrary('queryLib', 'any');
    $result = array();
    
    $result['privacyPolicy'] =  getconfig('policy_path').'/privacy/Wharf Street Studios Privacy policy.pdf';

    $this->setResponse('SUCCESS');
    return $result;
  }  
}