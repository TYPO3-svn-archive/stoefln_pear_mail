<?php
class ux_t3lib_htmlmail extends t3lib_htmlmail{


	/**
	* Adds a header to the mail. Use this AFTER the setHeaders()-function
	*
	* @param	string		$header: the header in form of "key: value"
	* @return	void
	*/
	public function add_header($header) {


        // Mail headers must be ASCII, therefore we convert the whole header to either base64 or quoted_printable
		if (!$this->dontEncodeHeader && !stristr($header,'Content-Type') && !stristr($header,'Content-Transfer-Encoding')) {
						// Field tags must not be encoded
			$parts = explode(': ',$header,2);
			if (count($parts) == 2) {
					$enc = $this->alt_base64 ? 'base64' : 'quoted_printable';
					$parts[1] = t3lib_div::encodeHeader($parts[1], $enc, $this->charset);
					$header = implode(': ', $parts);
			}
		}
 
		$this->headers .= $header."\n";
		if(!$this->headerArr)
			$this->headerArr = array();
		$parts = explode(': ',$header,2);
		$this->headerArr[$parts[0]] = $parts[1];
	}

	/**
	* Begins building the message-body and fixing the "boundary" issue
	*
	* @return	void
	*/
	public function setContent() {
		$this->message = '';
		$boundary = $this->getBoundary();

        // Setting up headers
		if (count($this->theParts['attach'])) {	// Generate (plain/HTML) / attachments
			$this->add_header('Content-Type: multipart/mixed; boundary="'.$boundary.'"');
			$this->add_message("This is a multi-part message in MIME format.\n");
			$this->constructMixed($boundary);
		} elseif ($this->theParts['html']['content']) {	// Generate plain/HTML mail
			$this->add_header('Content-Type: '.$this->getHTMLContentType() . '; boundary="'.$boundary.'"');
			$this->add_message("This is a multi-part message in MIME format.\n");
			$this->constructHTML($boundary);
		} else {	// Generate plain only
			$this->add_header($this->plain_text_header);
			$this->add_message($this->getContent('plain'));
		}
	}

	public function sendTheMail() {
		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpDebug']) t3lib_div::debug(Array('stoefln_pear_mail/htmlmail'=>'sendTheMail called.', 'File:Line'=>__FILE__.':'.__LINE__));

		$mailWasSent = false;
				// Sending the mail requires the recipient and message to be set.
		if (!trim($this->recipient) || !trim($this->message)) {
				return false;
		}

		// simulating the old behavior of sending with return-path
		if(strlen($this->returnPath) > 0)
		{
			$this->headerArr['Return-Path'] = $this->returnPath;
		};

		// send the Email
        $mailWasSent = $this->mail(
								$this->recipient,
								$this->subject,
								$this->message,
								$this->headers
								);
		if($mailWasSent){
			// if the mail was sent correctly sent to recipients in copy
			if ($this->recipient_copy){
                $this->mail(
                        $this->recipient_copy,
                        $this->subject,
                        $this->message,
                        $this->headers);
            }
					// Auto response
			if ($this->auto_respond_msg) {
                $theParts = explode('/',$this->auto_respond_msg,2);
                $theParts[1] = str_replace("/",chr(10),$theParts[1]);
                $this->mail(
                    $this->from_email,
                    $theParts[0],
                    $theParts[1],
                    "From: ".$this->recipient);
			};
		}

		return $mailWasSent;
	}

	// stoefln: pear lib used to send mails directly via smtp
	public function mail($to,$subject,$message,$headers=array())
	{

		require_once('Mail.php');

		// loading the extension configuration
		$smtpUser 		= $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpUser'];
		$smtpPassword	= $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpPassword'];
		$smtpServer		= $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpServer'];
		$smtpPort		= $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpPort'];
		$smtpDebug		= $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpDebug'];

		$headers = $this->headerArr;
  
		$headers['To']		= $to;
		$headers['Subject'] 	= $subject;

		$headers['Content-Transfer-Encoding'] = $this->alt_base64 ? 'base64' : 'quoted-printable';

		$body = $message;

		$params = array(
					'host' 		=> $smtpServer,
					'port' 		=> $smtpPort ? $smtpPort : 25,
					'auth' 		=> true,
					'username' 	=> $smtpUser,
					'password' 	=> $smtpPassword,
					'debug' 	=> $smtpDebug,
					);

		// Create the mail object using the Mail::factory method
		$mail_object =& Mail::factory('smtp', $params);
 		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpDebug']) t3lib_div::debug(Array('stoefln_pear_mail/htmlmail obj='=>$mail_object, '$params'=>$params, 'File:Line'=>__FILE__.':'.__LINE__));
		$mail_object->send($to, $headers, $body);

		if (PEAR::isError($mail_object)) {
            if ($smtpDebug){
                t3lib_div::debug(Array('stoefln_pear_mail/htmlmail'=>'PEAR::isError($mail_object) !!!!!!!!!!!!!!!!', 'File:Line'=>__FILE__.':'.__LINE__));
            }
            t3lib_div::debug($mail_object->getMessage());
            $error = true;

        } else {
            $error = false;
        };
		return !$error;

	}

}
?>
