<?php
/**
 * Secure login/registration user class.
 */

class User
{
    /** @var object $pdo Copy of PDO connection */
    private $pdo;
    /** @var object of the logged in user */
    private $user;
    /** @var string error msg */
    private $msg;
    /** @var int number of permitted wrong login attemps */
    private $permitedAttemps = 5;

    /**
     * Connection init function
     * @param string $conString DB connection string.
     * @param string $user DB user.
     * @param string $pass DB password.
     *
     * @return bool Returns connection success.
     */
    public function dbConnect($conString, $user, $pass)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            try {
                $pdo = new PDO($conString, $user, $pass);
                $this->pdo = $pdo;
                return true;
            } catch (PDOException $e) {
                $this->msg = 'Connection did not work out!';
                return false;
            }
        } else {
            $this->msg = 'Session did not start.';
            return false;
        }
    }

    /**
     * Send debug code to the Javascript console
     */
    public function debug_to_console($data)
    {
        if (is_array($data) || is_object($data)) {
            echo("<script>
				if(console.debug!='undefined'){
					console.log('PHP: " . json_encode($data) . "');
				}</script>");
        } else {
            echo("<script>
				if(console.debug!='undefined'){	
					console.log('PHP: " . $data . "');
				}</script>");
        }
    }

    /**
     * Return the logged in user.
     * @return user array data
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Login function
     * @param string $email User email.
     * @param string $password User password.
     *
     * @return bool Returns login success.
     */
    public function login($email, $password)
    {
        if (is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return false;
        } else {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare('SELECT id, fname, lname, email, wrong_logins, password, user_role FROM users WHERE email = ? and confirmed = 1 limit 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (password_verify($password, $user['password'])) {
                if ($user['wrong_logins'] <= $this->permitedAttemps) {
                    $this->user = $user;
                    session_regenerate_id();
                    $_SESSION['user']['id'] = $user['id'];
                    $_SESSION['user']['fname'] = $user['fname'];
                    $_SESSION['user']['lname'] = $user['lname'];
                    $_SESSION['user']['email'] = $user['email'];
                    $_SESSION['user']['user_role'] = $user['user_role'];
                    return true;
                } else {
                    $this->msg = 'This user account is blocked, please contact our support department.';
                    return false;
                }
            } else {
                $this->registerWrongLoginAttemp($email);
                $this->msg = 'Invalid login information or the account is not activated.';
                return false;
            }
        }
    }

    /**
     * Register a new user account function
     * @param string $email User email.
     * @param string $fname User first name.
     * @param string $lname User last name.
     * @param string $pass User password.
     * @return boolean of success.
     */
    public function registration($email, $fname, $lname, $pass)
    {
        $pdo = $this->pdo;
        if ($this->checkEmail($email)) {
            $this->msg = 'This email is already taken.';
            return false;
        }
        if (!(isset($email) && isset($fname) && isset($lname) && isset($pass) && filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $this->msg = 'Insert all valid requered fields.';
            return false;
        }

        $pass = $this->hashPass($pass);
        $confCode = $this->hashPass(date('Y-m-d H:i:s') . $email);
        $stmt = $pdo->prepare('INSERT INTO users (fname, lname, email, password, confirm_code) VALUES (?, ?, ?, ?, ?)');
        if ($stmt->execute([$fname, $lname, $email, $pass, $confCode])) {
            if ($this->sendConfirmationEmail($email)) {
                return true;
            } else {
                $this->msg = 'confirmation email sending has failed.';
                return false;
            }
        } else {
            $this->msg = 'Inesrting a new user failed.';
            return false;
        }
    }

    // Gets all names of the images stored in the database
    private function getAllImageNames() {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT name from images');

        if ($stmt->execute()) {
            return $stmt->fetch();
        } else {
            return false;
        }
    }

    // true = image is NOT in db
    // false = if image is in db
    // Checks if an image(name) is not in the database
    private function checkImageNotInDB($imageName) {
        if ($this->getAllImageNames() != false) {
            foreach ($this->getAllImageNames() as $imageNameInDB) {
                if ($imageName == $imageNameInDB) {
                    return false;
                }
            }
        }
        return true;
    }

    // Upload an image to db
    public function uploadImage($name, $image, $userID) {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('INSERT INTO images (name, image, userID) VALUES (?, ?, ?)');
        if ($this->checkImageNotInDB($name) && $stmt->execute([$name, $image, $userID])) {
            echo '<pre>Success</pre>';
            return true;
        } else {
            echo '<pre>Inserting a new image failed</pre>';
            return false;
        }
    }

    public function getImages($userID) {
        /** show image from 'images/' folder */
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT * from images WHERE userID = ?');
        if ($stmt->execute([$userID])) {
            while($row = $stmt->fetch()){
                echo ' <img height="300" width="300" src="images/'.$row['name'].' " /> ';
            }
        } else {
            return false;
        }

/*      mysqli style - probably connection not good.

        TODO: use parameterized userID. It is now set to 1 for testing purposes
        $db = mysqli_connect("localhost","root","","db_plastywood");
        debug_to_console($db);
        $sql = "SELECT image FROM images WHERE userID = 1";
        debug_to_console($sql);
        $sth = $db->query($sql);
        debug_to_console($sth);
        $result=mysqli_fetch_array($sth);
        debug_to_console($result);
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $result['image'] ).'"/>';
*/

/*      PDO style - not working

        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT image from images WHERE userID = ?');

        if ($stmt->execute([$userID])) {
            $file = null;
            $stmt->bindColumn(3, $file, PDO::PARAM_LOB);
            $stmt->fetch();
            header('Content-type: image/jpeg');
            echo $file;
        }
*/
    }

    /**
     * Email the confirmation code function
     * @param string $email User email.
     * @return boolean of success.
     */
    private function sendConfirmationEmail($email)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT confirm_code FROM users WHERE email = ? limit 1');
        $stmt->execute([$email]);

        $code = $stmt->fetch();

        $subject = 'Confirm your registration';
        $message = 'Please confirm you registration by pasting this code in the confirmation box: ' . $code['confirm_code'];
        $headers = 'X-Mailer: PHP/' . phpversion();

        if (mail($email, $subject, $message, $headers)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Activate a login by a confirmation code and login function
     * @param string $email User email.
     * @param string $confCode Confirmation code.
     * @return boolean of success.
     */
    public function emailActivation($email, $confCode)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE users SET confirmed = 1 WHERE email = ? and confirm_code = ?');
        $stmt->execute([$email, $confCode]);
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare('SELECT id, fname, lname, email, wrong_logins, user_role FROM users WHERE email = ? and confirmed = 1 limit 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            $this->user = $user;
            session_regenerate_id();
            if (!empty($user['email'])) {
                $_SESSION['user']['id'] = $user['id'];
                $_SESSION['user']['fname'] = $user['fname'];
                $_SESSION['user']['lname'] = $user['lname'];
                $_SESSION['user']['email'] = $user['email'];
                $_SESSION['user']['user_role'] = $user['user_role'];
                return true;
            } else {
                $this->msg = 'Account activitation failed.';
                return false;
            }
        } else {
            $this->msg = 'Account activitation failed.';
            return false;
        }
    }

    /**
     * Password change function
     * @param int $id User id.
     * @param string $pass New password.
     * @return boolean of success.
     */
    public function passwordChange($id, $pass)
    {
        $pdo = $this->pdo;
        if (isset($id) && isset($pass)) {
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            if ($stmt->execute([$id, $this->hashPass($pass)])) {
                return true;
            } else {
                $this->msg = 'Password change failed.';
                return false;
            }
        } else {
            $this->msg = 'Provide an ID and a password.';
            return false;
        }
    }


    /**
     * Assign a role function
     * @param int $id User id.
     * @param int $role User role.
     * @return boolean of success.
     */
    public function assignRole($id, $role)
    {
        $pdo = $this->pdo;
        if (isset($id) && isset($role)) {
            $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
            if ($stmt->execute([$id, $role])) {
                return true;
            } else {
                $this->msg = 'Role assign failed.';
                return false;
            }
        } else {
            $this->msg = 'Provide a role for this user.';
            return false;
        }
    }


    /**
     * User information change function
     * @param int $id User id.
     * @param string $fname User first name.
     * @param string $lname User last name.
     * @return boolean of success.
     */
    public function userUpdate($id, $fname, $lname)
    {
        $pdo = $this->pdo;
        if (isset($id) && isset($fname) && isset($lname)) {
            $stmt = $pdo->prepare('UPDATE users SET fname = ?, lname = ? WHERE id = ?');
            if ($stmt->execute([$id, $fname, $lname])) {
                return true;
            } else {
                $this->msg = 'User information change failed.';
                return false;
            }
        } else {
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

    /**
     * Check if email is already used function
     * @param string $email User email.
     * @return boolean of success.
     */
    private function checkEmail($email)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? limit 1');
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Register a wrong login attemp function
     * @param string $email User email.
     * @return void.
     */
    private function registerWrongLoginAttemp($email)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE users SET wrong_logins = wrong_logins + 1 WHERE email = ?');
        $stmt->execute([$email]);
    }

    /**
     * Password hash function
     * @param string $password User password.
     * @return string $password Hashed password.
     */
    private function hashPass($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
     * Print error msg function
     * @return void.
     */
    public function printMsg()
    {
        print $this->msg;
    }

    /**
     * Logout the user and remove it from the session.
     *
     * @return true
     */
    public function logout()
    {
        $_SESSION['user'] = null;
        session_regenerate_id();
        return true;
    }


    /**
     * List users function
     *
     * @return array Returns list of users.
     */
    public function listUsers()
    {
        if (is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare('SELECT id, fname, lname, email FROM users WHERE confirmed = 1');
            $stmt->execute();
            $result = $stmt->fetchAll();
            return $result;
        }
    }

    /**
     * Simple template rendering function
     * @param string $path path of the template file.
     * @return void.
     */
    public function render($path, $vars = '')
    {
        ob_start();
        include($path);
        return ob_get_clean();
    }

    /**
     * Template for index head function
     * @return void.
     */
    public function indexHead()
    {
        print $this->render(indexHead);
    }

    /**
     * Template for index top function
     * @return void.
     */
    public function indexTop()
    {
        print $this->render(indexTop);
    }

    /**
     * Template for login form function
     * @return void.
     */
    public function loginForm()
    {
        print $this->render(loginForm);
    }

    /**
     * Template for activation form function
     * @return void.
     */
    public function activationForm()
    {
        print $this->render(activationForm);
    }

    /**
     * Template for index middle function
     * @return void.
     */
    public function indexMiddle()
    {
        print $this->render(indexMiddle);
    }

    /**
     * Template for register form function
     * @return void.
     */
    public function registerForm()
    {
        print $this->render(registerForm);
    }

    /**
     * Template for index footer function
     * @return void.
     */
    public function indexFooter()
    {
        print $this->render(indexFooter);
    }

    /**
     * Template for user page function
     * @return void.
     */
    public function userPage()
    {
        $users = [];
        if ($_SESSION['user']['user_role'] == 2) {
            $users = $this->listUsers();
        }
        print $this->render(userPage, $users);
    }
}
