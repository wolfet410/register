<?php
/**
* Classes and functions
*/
class Common {

  public function log($str) {
    /**
    * Sends str to standard error log file
    *
    * @param $str: string containing the message to be logged
    */
    error_log('[REGISTER] ' . $str);
  }

  public function httpStatus($err) {
    /**
    * Echos standard HTTP status response
    *
    * @see Status code list: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
    *
    * @param $err: string containing the official HTTP status
    *
    * @return Echos content
    */
    switch ($err) {
      case '201':
        header('HTTP/1.1 201 Created');
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'
             . PHP_EOL . '<html><head>'
             . PHP_EOL . '<title>201 Created</title>'
             . PHP_EOL . '</head><body>'
             . PHP_EOL . '<h1>Created</h1>'
             . PHP_EOL . '<p>The object has been successfully created.</p>'
             . PHP_EOL . '</body></html>';
        break;
      case '204':
        header('HTTP/1.1 204 No Content');
        break;
      case '400':
        header('HTTP/1.1 400 Bad Request');
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'
             . PHP_EOL . '<html><head>'
             . PHP_EOL . '<title>400 Bad Request</title>'
             . PHP_EOL . '</head><body>'
             . PHP_EOL . '<h1>Bad Request</h1>'
             . PHP_EOL . '<p>Your browser sent a request that this server could not understand.</p>'
             . PHP_EOL . '</body></html>';
        break;
      case '401.1':
        header('HTTP/1.1 401.1 Logon Failed');
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'
             . PHP_EOL . '<html><head>'
             . PHP_EOL . '<title>401.1 Logon Failed</title>'
             . PHP_EOL . '</head><body>'
             . PHP_EOL . '<h1>Logon Failed</h1>'
             . PHP_EOL . '<p>The logon attempt is unsuccessful, probably because of a user name or password that is not valid.</p>'
             . PHP_EOL . '</body></html>';
        break;
      case '403':
        header('HTTP/1.1 403 Forbidden');
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'
             . PHP_EOL . '<html><head>'
             . PHP_EOL . '<title>403 Forbidden</title>'
             . PHP_EOL . '</head><body>'
             . PHP_EOL . '<h1>Forbidden</h1>'
             . PHP_EOL . '<p>The server understood the request, but is refusing to fulfill it.</p>'
             . PHP_EOL . '</body></html>';
        break;
      case '405':
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET, POST, PUT, DELETE');
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'
             . PHP_EOL . '<html><head>'
             . PHP_EOL . '<title>405 Method Not Allowed</title>'
             . PHP_EOL . '</head><body>'
             . PHP_EOL . '<h1>Method Not Allowed</h1>'
             . PHP_EOL . '<p>Your browser attempted to use a method unsupported by this server.</p>'
             . PHP_EOL . '</body></html>';
        break;
    }
  }

  public function pdoDb() {
    /**
    * Returns a PDO class so the parameters are only defined once
    *
    * @return PDO class
    */
    $pdo = new PDO('mysql:host=localhost;dbname=register.bwlw;charset=utf8', 'register.bwlw', 
         'register.bwlw08072014', array(PDO::ATTR_EMULATE_PREPARES => false, 
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    return $pdo;
  }

}
?>