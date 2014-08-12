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