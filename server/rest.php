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
    unset($r);
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        switch ($this->getPathObject() . '/' . $this->getPathDetail()) {
          case 'accounts/':
            $auth = new Auth;
            $uuid = $auth->getUuid();
            if ($uuid === FALSE) {
              Common::log('rest.php, get, case accounts/, bad auth');
              Common::httpStatus('401.1');
            } else {
              $rg = new Register;
              $listOfAccounts = $rg->getAccounts($uuid);
              if ($listOfAccounts === FALSE) {
                Common::log('rest.php, parseRequest, case accounts/, error returned from getAccounts');
                Common::httpStatus('204');
              } else {
                echo $listOfAccounts;
              }
            }
            break;
          case 'objects/detail':
            Common::log('rest.php, parseRequest, switch on path object: Objects and detail in URL, ie. http://.../objects/detail/');
            break;
          case '400':
          default:
            Common::log('rest.php, parseRequest, switch on path object, HTTP 400: Bad request in URL');
            Common::httpStatus('400');
            break;
        }
        break;
      case 'POST':
        switch ($this->getPathObject() . '/' . $this->getPathDetail()) {
          case 'users/create':
            $auth = new Auth;
            $r = $auth->createUser();
            if ($r === 0) {
                Common::httpStatus('201');
            } else {
              Common::log('rest.php, post, case users/create, createUser returned failure');
              Common::httpStatus('403');
            }
            break;
          case 'accounts/create':
            $auth = new Auth;
            $uuid = $auth->getUuid();
            if ($uuid === FALSE) {
              Common::log('rest.php, post, case accounts/create, bad auth');
              Common::httpStatus('401.1');
            } else {
              $rg = new Register;
              $r = $rg->createAccount($uuid);
              if ($r === 0) {
                Common::httpStatus('201');
              } else {
                Common::log('rest.php, post, case accounts/create, createAccount returned failure');
                Common::httpStatus('403');
              } 
              break;
            }
        }
        break;
      case 'PUT':
        Common::log('PUT');
        break;
      case 'DELETE':
        Common::log('DELETE');
        break;
      default:
        Common::log('rest.php, parseRequest, switch on method, HTTP 405: Invalid method sent in request');
        Common::httpStatus('405');
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
