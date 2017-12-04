<?php namespace ProcessWire;

class ApiHelper
{
  public static function noEndPoint() {
    return 'No Endpoint specified!';
  }
  
  public static function checkRequiredParameters($data, $params) {
    foreach ($params as $param) {
      if (!isset($data->$param)) throw new \Exception('Required parameter "' . $param .'" missing!', 400);
    }
  }

  public static function baseUrl() {
    return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
  }
}