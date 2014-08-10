<?php
/**
* Helper functions
**/
function __autoload($className) {
  /**
  * Autoloads PHP files based on lower case of class name
  *
  * @see http://php.net/manual/en/language.oop5.autoload.php
  */
  require(strtolower($className) . '.php');
}

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

}
?>