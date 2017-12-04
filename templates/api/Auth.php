<?php namespace ProcessWire;

use \Firebase\JWT\JWT;

class Auth
{
  public static function auth() 
  {
    if(wire('user')->isGuest())
      throw new \Exception('user is not logged in', 401);

    if(!isset(wire('config')->jwtSecret)) {
      throw new \Exception('incorrect site config', 500);
    }

    $issuedAt = time();
    $notBefore = $issuedAt;
    $expire = $notBefore + wire('config')->sessionExpireSeconds;
    $serverName = wire('config')->httpHost;

    $token = array(
      "iss" => $serverName, // issuer
      "aud" => $serverName, // audience
      "iat" => $issuedAt, // issued at
      "nbf" => $notBefore, // valid not before
      "exp" => $expire, // token expire time
    );

    $jwt = JWT::encode($token, wire('config')->jwtSecret);

    $response = new \StdClass();
    $response->jwt = $jwt;
    return $response;
  }

  public static function login($data) {
    ApiHelper::checkRequiredParameters($data, ['username', 'password']);
    
    $data->username = wire('sanitizer')->selectorValue($data->username);
    $data->password = wire('sanitizer')->string($data->password);

    $user = wire('users')->get($data->username);

    // if(!$user->id) throw new \Exception("User with username: $data->username not found", 404);
    // prevent username sniffing by just throwing a general exception:
    if(!$user->id) throw new \Exception("Login not successful", 401); 

    $loggedIn = wire('session')->login($data->username, $data->password);

    if($loggedIn) return self::auth();
    else throw new \Exception("Login not successful", 401); 
  }

  public static function logout() {
    wire('session')->logout(wire('user'));
    return 'user logged out';
  }
}