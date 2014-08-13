<?php
/**
* Classes and functions
*/
class Register {

  public function getAccounts($uuid) {
    /**
    * Gets a list of accounts that current user has rights to
    *
    * @example curl -v --user wolfet410@gmail.com:pwd http://register.datatechcafe.net/accounts
    *
    * @param uuid: Integer of User UID
    *
    * @return List of accounts in JSON
    */
    $db = Common::pdoDb();
    $stmt = $db->prepare('SELECT Account.name, Account.balance FROM Account LEFT JOIN UserAccount '
                         . 'ON UserAccount.auid = Account.auid WHERE UserAccount.uuid = :uuid');
    $stmt->execute(array(':uuid'=>$uuid));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rows === FALSE || count($rows) < 1) {
      Common::log('register.php, getAccounts: no results');
      return FALSE;
    } else {
      return json_encode($rows);
    }
  }

  public function createAccount($uuid) {
    /**
    * Creates an account for the currently logged in user
    *
    * @example curl -v -d "name=Checking&balance=10.00" --user wolfet410@gmail.com:pwd http://register.datatechcafe.net/accounts/create
    *
    * @param uuid: Integer of User UID
    *
    * @return 0 on success, other on failure
    */
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    $balance = filter_input(INPUT_POST, 'balance', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);    
    if (!isset($name) || !isset($balance)) {
      Common::log('register.php, createAccounts: Missing POST values');
      return 1;
    }
    $db = Common::pdoDb();
    $stmt = $db->prepare('INSERT INTO Account (uuid, name, balance) VALUES (:uuid, :name, :balance)');
    $stmt->execute(array(':uuid'=>$uuid, ':name'=>$name, ':balance'=>$balance));
    if ($stmt->rowCount() !== 1) {
      Common::log('register.php, createAccounts: Failed to insert new account into DB');
      return 2;
    } else {
      return 0;
    }
  }
}
?>