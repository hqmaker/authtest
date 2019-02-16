<?php
class Auth
{
    /**
     * Db connection object.
     *
     * @var MysqlDatabase
     */
    private $db;
    private $username;
    private $userId;

    /**
     * @param MysqlDatabase $db
     */
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    /**
     * Return crypted password with provided salt.
     *
     * @param $pw
     * @param $salt
     * @return string
     */
    public function cryptThePass($pw, $salt)
    {
        return crypt($pw, $salt);
    }

    /**
     * Returns random blowfish salt.
     * TODO: add more salt types.
     *
     * @return string
     */
    public function getRandomSalt()
    {
        $salt = '$2y$14$';
        for ($i = 0; $i < 22; $i++) {
            $salt .= './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'[mt_rand(0, 63)];
        }

        return $salt;
    }

    /**
     * Check if mamber authenticated
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        if (isset($_SESSION['username'], $_SESSION['authKey'])) {
            $username = $_SESSION['username'];
            $username = $this->db->escape_string($username);

            $query = 'SELECT
          					    password,
          					    salt,
                        user_id,
                        username
          				      FROM
          				  	    users
                        WHERE
          				  	    username="' . $username . '"';

            $dbResult = $this->db->query($query);
            list($dbPass, $dbSalt, $dbId, $dbUsername) = $this->db->fetch_row($dbResult);
            $dbPass = (string)$dbPass;
            $dbSalt = (string)$dbSalt;

            if ($dbPass !== '' && $dbSalt !== '') {
                $hashCookies = hash('sha512', $dbPass . $_SERVER['REMOTE_ADDR']);

                if ($hashCookies === $_SESSION['authKey']) {
                    $this->userId = $dbId;
                    $this->username = $dbUsername;

                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Get user Hash by username/pass provided.
     *
     * @param string $username
     * @param string $pass
     * @return string
     */
    public function getMemberHash($username, $pass)
    {
        $username = $this->db->escape_string($username);
        $pass = $this->db->escape_string($pass);

        $query = 'SELECT
                    user_id,
                    password,
                    salt,
                    username
                  FROM
                    users
                  WHERE
                    username="' . $username . '"';

        $dbResult = $this->db->query($query);
        list($dbId, $dbPass, $dbSalt, $dbUsername) = $this->db->fetch_row($dbResult);

        $dbPass = (string)$dbPass;
        $dbSalt = (string)$dbSalt;

        if ($dbPass !== '' && $dbSalt !== '') {
            $hash = $this->cryptThePass($pass, $dbSalt);

            $this->username = $dbUsername;
            $this->userId = $dbId;

            if ($hash === $dbPass) {
                return $hash;
            }
        }

        return '';
    }

    /**
     * Get client id from db by username
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function authenticateUser($username, $pass)
    {
        $hash = $this->getMemberHash($username, $pass);

        if ($hash !== '') {
            $hashCookies = hash('sha512', $hash . $_SERVER['REMOTE_ADDR']);

            $_SESSION['username'] = $username;
            $_SESSION['authKey'] = $hashCookies;

            return true;
        }

        return false;
    }


    public function registerProfile($username, $password, $fullname, $email)
    {
        $salt = $this->getRandomSalt();
        $password = $this->cryptThePass($password, $salt);

        $key = hash('sha512', $username . $password . $_SERVER['REMOTE_ADDR']);
        $key = $this->db->escape_string($key);

        $username = $this->db->escape_string($username);
        $password = $this->db->escape_string($password);
        $fullname = $this->db->escape_string($fullname);
        $email = $this->db->escape_string($email);
        $created = date('Y-m-d');

        $query = 'INSERT INTO
					users
					(username,
					password,
          salt,
          fullname,
          email,
          created,
          activationKey,
          active)
				  VALUES
				  	("' . $username . '",
				  	"' . $password . '",
				  	"' . $salt . '",
				  	"' . $fullname . '",
				  	"' . $email . '",
				  	"' . $created . '",
				  	"' . $key . '",
				  	0)';
        $dbResult = $this->db->query($query);

        if ($dbResult) {
          return true;
        }

        return false;
    }

    public function isUsername($username)
    {
        $query = 'SELECT
                    username
                  FROM
                    users
                  WHERE
                    lower(username)="' . $this->db->escape_string($username) . '"';

        $dbResult = $this->db->query($query);

        return $this->db->num_rows($dbResult);
    }

    public function isEmail($email)
    {
        $query = 'SELECT
                    user_id
                  FROM
                    users
                  WHERE
                    lower(email)="' . $this->db->escape_string($email) . '"';

        $dbResult = $this->db->query($query);

        return $this->db->num_rows($dbResult);
    }

    public function clientLogout()
    {
        unset($_SESSION['email'], $_SESSION['authKey']);
    }
}
