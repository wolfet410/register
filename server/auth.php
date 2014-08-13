<?php
/**
* Classes and functions
*/
class Auth {

  public function getUuid() {
    /**
    * Verifies user based on HTTP authentication header
    *
    * @example curl -v --user wolfet410@gmail.com:pwd http://register.datatechcafe.net/accounts
    *
    * @param Authentication header
    * 
    * @return uuid on successful verification, false on failure
    */
    // PHP_AUTH constants automatically base64 decode username and password
    $user = $_SERVER['PHP_AUTH_USER'];
    $pw = $_SERVER['PHP_AUTH_PW'];
    if (!$user || !$pw) {
      Common::log('auth.php, getUuid: empty user or pw');
      return FALSE;
    }

    $db = Common::pdoDb();
    $stmt = $db->prepare("SELECT hash FROM User WHERE email=:email LIMIT 1");
    $stmt->execute(array(':email'=>$user));
    $hashes = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($hashes === FALSE || $hashes['hash'] === null) {
      Common::log('auth.php, getUuid: cant get hash');
      return FALSE;
    }
    $hasher = new PasswordHash(8, FALSE);
    if ($hasher->CheckPassword($pw, $hashes['hash'])) {
      // Verified password, continue
      $stmt = $db->prepare("SELECT uuid FROM User WHERE email=:email LIMIT 1");
      $stmt->execute(array(':email'=>$user));
      $rows = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($rows === FALSE || $rows['uuid'] === null || $rows['uuid'] < 1) {
        Common::log('auth.php, getUuid: bad uuid');
        return FALSE;
      } else {
        return $rows['uuid'];
      }
    } else {
      Common::log('auth.php, getUuid: bad password');
      return FALSE;
    }
  }

  public function createUser() {
    /**
    * Creates a user account via POST request
    * 
    * @example curl -d "name=Todd%20Wolfe&email=wolfet410@gmail.com&password=pwd" http://register.datatechcafe.net/users/create
    *
    * @param $name: String, person's name; $email: String, email addr; $password: String, user-entered password
    * 
    * @return 0 on success, 1 on failure, 2 if user already exists
    */
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    if (!$name || !$email || !$password) {
      Common::log('auth.php, createUser: empty name, email, or password');
      return 1;
    }

    // Check if user already exists
    $db = Common::pdoDb();
    $stmt = $db->prepare('SELECT COUNT(*) FROM User WHERE email=:email');
    // $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute(array(':email'=>$email));
    if ($stmt->fetchColumn() > 0) {
      // User already exists
      Common::log('auth.php, createUser: Email address already exists');
      return 2;
    }

    // Hash password
    $hasher = new PasswordHash(8, FALSE);
    $hash = $hasher->HashPassword($password);
    if (strlen($hash) < 20) {
      // Hashing password failed
      Common::log('auth.php, createUser: Password hash failed');
      return 3;
    }

    // Write new user account to database
    $db = Common::pdoDb();
    $stmt = $db->prepare('INSERT INTO User (name, email, hash) VALUES (:name, :email, :hash)');
    $stmt->execute(array(':name'=>$name, ':email'=>$email, ':hash'=>$hash));
    if ($stmt->rowCount() !== 1) {
      Common::log('auth.php, createUser: Failed to insert new user into DB');
      return 4;
    } else {
      return 0;
    }
  }
}