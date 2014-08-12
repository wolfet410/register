<?php
/**
* Classes and functions
*/
class Register {

  public function getAccounts($uuid) {
    /**
    * Gets a list of accounts that current user has rights to
    *
    * @example curl -v --user wolfet410@gmail.com:password http://register.datatechcafe.net/accounts
    *
    * @param uuid: Integer of User UID
    *
    * @return List of accounts in JSON
    *
    * @todo Write in authentication modules & check
    */
    $accounts = array('Account 1','Account 2','Account 3');
    return json_encode($accounts);
  }

}
?>