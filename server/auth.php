<?php
/**
* Classes and functions
*/
class Auth {

  public function getUuid() {
    /**
    * Verifies user based on HTTP authentication header
    *
    * @example curl -v --user wolfet410@gmail.com:password http://register.datatechcafe.net/accounts
    *
    * @param Authentication header
    * 
    * @return uuid on successful verification, false on failure
    */
    // These $_SERVER variables automatically take care of base64 decoding the username
    // and password, which are automatically base64 encoded by curl
    // The real question, though, is how to handle this same thing with Titanium, 
    // because I suspect that I'll have to manually base64 encode the username and password
    // there. Not a big deal, though.
    // And some links on how to do exactly that on the Titanium side:
    // http://mark.biek.org/blog/2010/07/basic-authentication-with-titanium-network-httpclient
    // https://developer.appcelerator.com/question/64561/basic-authentication-within-createhttpclient
    // Handling user names & passwords over 72 characters, which I should do since 
    // my username is going to be an email address: http://yaymedia.net/?p=1323
    // Don't forget!! if (strlen($password) > 72) { die("Password must be 72 characters or less"); }
    $user = $_SERVER['PHP_AUTH_USER'];
    Common::log('auth.php, getUuid, user: ' . $user);
    $pwdin = $_SERVER['PHP_AUTH_PW'];
    Common::log('auth.php, getUuid, password: ' . $pwdin);
    $hasher = new PasswordHash(8, FALSE);
    $pwdhash = $hasher->HashPassword($pwdin);
    Common::log('auth.php, getUuid, password: ' . $pwdhash);
    // Once I get the username and password sent (which I can already do via curl for testing PHP), 
    // I need to use PDO to query the user database
    // Remember, we are planning to hash the password in the database, so we'll have to deal with 
    // hashing the received password for the comparison!
    // Note, some articles I'm reading also discuss using session IDs because password hashing is expensive, 
    // def something to consider

    $db = Common::pdoDb();
    $stmt = $db->prepare("SELECT * FROM User WHERE email=:email LIMIT 1");
    $stmt->bindValue(':email', $user, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $rows['uuid'] . ',' . $rows['name'] . ',' . $rows['email'] . ',' . $rows['hash'];
  }

  public function createUser() {
    /**
    * Creates a user account via POST request
    * 
    * @example curl -d "name=Todd%20Wolfe&email=wolfet411@gmail.com&password=pwd" http://register.datatechcafe.net/users/create
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