<?php namespace ProcessWire;

require_once dirname(__FILE__) . "/ApiHelper.php";

class Test
{
  public static function getSomeData() {
    // return 'Api Endpoint: ' . date(DATE_ISO8601);
    $data = new \StdClass();
    $data->user = wire('user')->name;

    return $data;
  }

  public static function postWithSomeData($data) {
    // Check for required parameter "message" and sanitize with PW Sanitizer
    $data = ApiHelper::checkAndSanitizeRequiredParameters($data, ['message|text']);

    return "Your message is: " . $data->message;
  }
}