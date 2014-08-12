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
* Bootstrap
*/
$rest = new Rest;
$rest->parseRequest();

/**
* Classes and functions
*/
class Rest {  

  public function parseRequest() {
    /**
    * Parses request for REST components
    *
    * @return HTTP response code, based on response from function I guess?
    *
    * @todo First function (accounts)
    *       Return each account name or a particular user, based on the auth header
    *       If an auth header isn't found, reply with a 401 Unauthorized
    */
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        switch ($this->getPathObject() . '/' . $this->getPathDetail()) {
          case 'accounts/':
            $auth = new Auth;
            $uuid = $auth->getUuid();
            $rg = new Register;
            $listOfAccounts = $rg->getAccounts($uuid);
            echo $listOfAccounts;
            break;
          case 'objects/detail':
            Common::log('rest.php, parseRequest, switch on path object: Objects and detail in URL, ie. http://.../objects/detail/');
            break;
          case '400':
          default:
            header('HTTP/1.1 400 Bad Request');
            echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'
                 . PHP_EOL . '<html><head>'
                 . PHP_EOL . '<title>400 Bad Request</title>'
                 . PHP_EOL . '</head><body>'
                 . PHP_EOL . '<h1>Bad Request</h1>'
                 . PHP_EOL . '<p>Your browser sent a request that this server could not understand.</p>'
                 . PHP_EOL . '</body></html>';
            Common::log('rest.php, parseRequest, switch on path object, HTTP 400: Bad request in URL');
            break;
        }
        break;
      case 'POST':
        switch ($this->getPathObject() . '/' . $this->getPathDetail()) {
          case 'users/create':
            $auth = new Auth;
            $auth->createUser();
            break;
        }
        break;
      case 'PUT':
        Common::log('PUT');
        break;
      case 'DELETE':
        Common::log('DELETE');
        break;
      default:
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET, POST, PUT, DELETE');
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'
             . PHP_EOL . '<html><head>'
             . PHP_EOL . '<title>405 Method Not Allowed</title>'
             . PHP_EOL . '</head><body>'
             . PHP_EOL . '<h1>Method Not Allowed</h1>'
             . PHP_EOL . '<p>Your browser attempted to use a method unsupported by this server.</p>'
             . PHP_EOL . '</body></html>';
        Common::log('rest.php, parseRequest, switch on method, HTTP 405: Invalid method sent in request');
        break;
    };
  }

  private function getUrlPath($url) {
    /**
    * Pulls the path out of the URL string
    *
    * @param $url: string containing the full URL
    * @return string of the path part of the URL
    */
    $uri = parse_url($url);
    return $uri['path'];
  }

  private function getPathObject() {
    /**
    * Object is the first level of items listed in the path
    * path = /api/object/detail
    *
    * @return Lowercase string of the path's object
    *
    * @todo Error check for array length and return 404 if we're missing
    *       the object portion of the URL
    */
    $paths = explode('/', $this->getUrlPath($_SERVER['REQUEST_URI']));
    // Return the path's object portion of the array
    $object = isset($paths[1]) ? strtolower($paths[1]) : '400';
    return $object;
  }

  private function getPathDetail() {
    /**
    * Detail is the second level of items listed in the path
    * path = /api/object/detail
    *
    * @return Losercase string of the path's detail
    *
    * @todo Error check for array length and return 404 if we're missing
    *       the detail portion of the URL
    */
    $paths = explode('/', $this->getUrlPath($_SERVER['REQUEST_URI']));
    // Return the path's object portion of the array
    $detail = isset($paths[2]) ? strtolower($paths[2]) : '';
    return $detail;
  }

}
?>
