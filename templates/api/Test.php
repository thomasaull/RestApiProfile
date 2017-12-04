<?php namespace ProcessWire;

class Test
{
  public static function getSomeData() 
  {
    // return 'Api Endpoint: ' . date(DATE_ISO8601);
    $data = new \StdClass();
    $data->user = wire('user')->name;

    return $data;
  }
}