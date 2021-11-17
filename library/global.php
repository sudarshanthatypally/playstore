<?php
  function getConfig($key)
  {
    return isset(autoload::$_CONFIG[$key])?autoload::$_CONFIG[$key]:"";
  }
  
  function setConfig($key, $value)
  {
    autoload::$_CONFIG[$key] = $value;
    return isset(autoload::$_CONFIG[$key])?autoload::$_CONFIG[$key]:"";
  }
 
  function getString($key)
  {
    return isset(autoload::$_STRING[$key])?autoload::$_STRING[$key]:"";
  }
  
  function getComponentUrl($moduleName, $componentName, $params = array())
  {
    return '?module='.$moduleName.'&component='.$componentName."&".http_build_query($params);
  }
  
  function isPost()
  {
    return ($_SERVER['REQUEST_METHOD'] == 'POST')? true:false;
  }
  
  function getUserId()
  {
    return ($_SESSION['user_id']>0)?$_SESSION['user_id']:0;
  }
  
  function getUserRole()
  {
   return $_SESSION['user_role'];
  }
  
  function isAdmin()
  {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] == USER_ROLE_ADMIN)?true:false;
  }
  
  function isUserRole($roleId)
  {
    return (getUserRole()==$roleId)?true:false;
  }
  
  function isOwner($userId)
  {
    return ($userId==getUserId())?true:false;
  }
  
  function isLoggedInUser()
  {
    if(isset($_SESSION['user_id']) && $_SESSION['user_id']!='')
    {
      return true;
    } else {
      return false;
    }
  }
  
  function sendAjaxResponse($status=true, $message="", $options=array())
  {
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header('Content-Type: application/json');
    echo json_encode(array('status'=>$status, 'message'=>$message));
    die();
  } 
  
  function formatArr($arr, $key, $options = array())
  {
    $temp = array();
    foreach ($arr as $item) {
      $temp[$item[$key]] = $item;
    }

    return $temp;
  }

  function print_log($log_msg)
  {
      $log_filename = $_SERVER['DOCUMENT_ROOT']."/EPIKO/log";
      $now   = time();

      $path = $log_filename.'/';
      if ($handle = opendir($path)) {
          while (false !== ($file = readdir($handle))) { 
              $filelastmodified = filemtime($path . $file);
              //24 hours in a day * 3600 seconds per hour
              if(is_file($file)){
                if((time() - $filelastmodified) >= 10*24*3600) // 10days
                {
                  unlink($path . $file);
                }
              }
          }
          closedir($handle); 
      }
      /*$stream=fopen('s3://epiko-playstore-logs','a');
      fwrite($stream, 'Hello');
      fclose($stream);*/
      //$log_filename = "/log";
      if (!file_exists($log_filename)) 
      {
          // create directory/folder uploads.
          mkdir($log_filename, 0777, true);
      }
      $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
      // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
      file_put_contents($log_file_data, json_encode($log_msg, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
  } 
  function logServer($apiName,$logMessage){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,"http://35.176.252.22/EPIKO/staging/rest.php");
    curl_setopt($ch, CURLOPT_POST, 0);
    /*curl_setopt($ch, CURLOPT_POSTFIELDS,
                "postvar1=value1&postvar2=value2&postvar3=value3");*/
    curl_setopt($ch, CURLOPT_POSTFIELDS, 
                http_build_query(array('applicationKey' => '12345',
                                        'methodName' => $apiName,
                                        'log_message' => $logMessage)));
    // In real life you should use something like:
    // curl_setopt($ch, CURLOPT_POSTFIELDS, 
    //          http_build_query(array('postvar1' => 'value1')));

    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close ($ch);
      return $server_output;
    // Further processing ...
    //if ($server_output == "OK") { ... } else { ... }
  }
  function serverLog($apiName, $text, $level='i') {
    date_default_timezone_set("Asia/Kolkata");
    switch (strtolower($level)) {
        case 'e':
        case 'error':
            $level='ERROR';
            break;
        case 'i':
        case 'info':
            $level='INFO';
            break;
        case 'd':
        case 'debug':
            $level='DEBUG';
            break;
        default:
            $level='INFO';
    }

    $log_filename = $_SERVER['DOCUMENT_ROOT']."/EPIKO/playstore_log";

      $path = $log_filename.'/';
      if ($handle = opendir($path)) {
          while (false !== ($file = readdir($handle))) { 
              $filelastmodified = filemtime($path . $file);
              //24 hours in a day * 3600 seconds per hour
              if(!empty($file)){
                if(!empty($filelastmodified)){
                  if((time() - $filelastmodified) > 10*24*3600) // 10days
                  {
                    unlink($path . $file);
                  }
                }
                
              }
              
          }
          closedir($handle); 
      }
      if (!file_exists($log_filename)) 
      {
          // create directory/folder uploads.
          mkdir($log_filename, 0777, true);
      }
      $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
      // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
      $msgData = date("[Y-m-d H:i:s]")."\t[".$level."]\t[".$apiName."]\t".$text;
      file_put_contents($log_file_data, $msgData . "\n", FILE_APPEND);
  } 

?>