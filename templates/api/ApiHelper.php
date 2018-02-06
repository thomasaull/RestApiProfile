<?php namespace ProcessWire;

class ApiHelper
{
  public static function noEndPoint() {
    return 'No Endpoint specified!';
  }

  public static function checkAndSanitizeRequiredParameters($data, $params) {
    foreach ($params as $param) {
      // Split param: Format is name|sanitizer
      $name = explode('|', $param)[0];
      $sanitizer = explode('|', $param)[1];

      // Check if Param exists
      if (!isset($data->$name)) throw new \Exception('Required parameter "' . $param .'" missing!', 400);

      // Sanitize Data
      if (!$sanitizer) {
        \TD::fireLog('WARNING: No Sanitizer specified for: ' . $name . ' Applying default sanitizer: text');
        $data->$name = wire('sanitizer')->text($data->$name);
      }

      $data->$name = wire('sanitizer')->$sanitizer($data->$name);
    }

    return $data;
  }

  public static function baseUrl() {
    // $site->urls->httpRoot
    return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";
  }
}
