<?php
/**
 * wrapper for the pear Mail class
 */
require_once('Mail.php');

class pearmail{

    private $debug = false;
    private $port = 25;
    private $auth = true;
    private $username;
    private $password;
    private $host;

    public function getHost() {
        return $this->host;
    }

    /**
     * sets the host address (e.g. mail.gmx.net)
     * @param <type> $host
     */
    public function setHost($host) {
        $this->host = $host;
    }

    public function getDebug() {
        return $this->debug;
    }

    /**
     * set to true for debugging the communication
     * @param boolean $debug
     */
    public function setDebug($debug) {
        $this->debug = $debug;
    }

    public function getPort() {
        return $this->port;
    }

    /**
     * set the port. default is 25
     * @param int $port
     */
    public function setPort($port) {
        $this->port = $port;
    }

    public function getAuth() {
        return $this->auth;
    }

    public function setAuth($auth) {
        $this->auth = $auth;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }


    /**
     * finally sends the mail
     * @param <type> $email
     * @param <type> $subject
     * @param <type> $message
     * @param <type> $headers
     * @return boolean success=true or failure=false
     */
    public function send($email, $subject, $message, $headers){
        $params = array(
					'host' 		=> $this->getHost(),
					'port' 		=> $this->getPort(),
					'auth' 		=> $this->getAuth(),
					'username' 	=> $this->getUsername(),
					'password' 	=> $this->getPassword(),
					'debug' 	=> $this->getDebug(),
					);

		// Create the mail object using the Mail::factory method
		$smtpMail =& Mail::factory('smtp', $params);
 		if ($this->debug){
            t3lib_div::debug(Array('smtpMail='=>$smtpMail, '$params'=>$params, 'File:Line'=>__FILE__.':'.__LINE__));
        }
        $smtpMail->send($email, $headers, $message);
        $error = false;

		if (PEAR::isError($smtpMail)) {
            if ($this->debug){
                t3lib_div::debug(Array('smtpMail'=>'PEAR::isError($mail_object) !!!!!!!!!!!!!!!!', 'File:Line'=>__FILE__.':'.__LINE__));
            }
            t3lib_div::debug($smtpMail->getMessage());
            $error = true;
        }
        
		return !$error;
    }
}

?>
