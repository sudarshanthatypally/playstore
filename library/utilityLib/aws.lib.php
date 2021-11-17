<?php

require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\ElasticTranscoder\ElasticTranscoderClient;

class aws{
  //Singleton
  protected static $objInstance;

  public static function get(){
    if(!isset(self::$objInstance)){
      $class=__CLASS__;
      self::$objInstance=new $class;
    }
    return self::$objInstance;
  }

  public function uploadToS3FromLocalFile($sourceFile, $targetPath, $options=array())
  {
    $info = pathinfo($sourceFile);
    $targetPath =  $targetPath.'.'.$info['extension'];
    $s3 = S3Client::factory(array(
      'key' =>  getConfig('amazons3_access_key'),
      'secret' => getConfig('amazons3_secret_key'),
      'region' => getConfig('aws_region')
    ));
    $type = 'image/png'; //to display image
    $result = $s3->putObject(array(
      'Bucket'       => getConfig('amazons3_bucket_name'),
      'Key'          => $targetPath,
      'SourceFile'   => $sourceFile,
      'ContentType'  => $type,
      'ACL'          => 'public-read',
      'StorageClass' => 'REDUCED_REDUNDANCY'
    ));

    return array('result' => $result, 'extension' => $info['extension']);
  }

  public function uploadToS3($sourceFile, $targetPath, $options=array())
  {
    $info = pathinfo($sourceFile['name']);
    $info['extension'] = explode("?", $info['extension'])[0];
    $targetPath =  $targetPath.'.'.$info['extension'];

    $s3 = S3Client::factory(array(
      'key' =>  getConfig('amazons3_access_key'),
      'secret' => getConfig('amazons3_secret_key'),
      'region' => getConfig('aws_region')
    ));

    $result = $s3->putObject(array(
      'Bucket'       => getConfig('amazons3_bucket_name'),
      'Key'          => $targetPath,
      'SourceFile'   => $sourceFile['tmp_name'],
      'ContentType'  => $sourceFile['type'],
      'ACL'          => 'public-read',
      'StorageClass' => 'REDUCED_REDUNDANCY'
    ));

    return array('result' => $result, 'extension' => $info['extension']);
  }

  public function generateURL($object,$time)
  {
    $s3 = S3Client::factory(array(
      'key' =>  getConfig('amazons3_access_key'),
      'secret' => getConfig('amazons3_secret_key'),
      'region' => getConfig('aws_region')
    ));

    $url = $s3->getObjectUrl('jvideo-transcode', $object, $time);
    return $url;
  }

  
}
?>
