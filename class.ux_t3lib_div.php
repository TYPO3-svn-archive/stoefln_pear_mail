<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2009 Kasper Skaarhoj (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * The legendary "t3lib_div" class - Miscellaneous functions for general purpose.
 * Most of the functions does not relate specifically to TYPO3
 * However a section of functions requires certain TYPO3 features available
 * See comments in the source.
 * You are encouraged to use this library in your own scripts!
 *
 * USE:
 * The class is intended to be used without creating an instance of it.
 * So: Don't instantiate - call functions with "t3lib_div::" prefixed the function name.
 * So use t3lib_div::[method-name] to refer to the functions, eg. 't3lib_div::milliseconds()'
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage t3lib
 */
require_once t3lib_extMgm::extPath('stoefln_pear_mail').'lib/class.pearmail.php';

final class ux_t3lib_div extends t3lib_div {


    /**
	 * Simple substitute for the PHP function mail() which allows you to specify encoding and character set
	 * The fifth parameter ($encoding) will allow you to specify 'base64' encryption for the output (set $encoding=base64)
	 * Further the output has the charset set to ISO-8859-1 by default.
	 * Usage: 4
	 *
	 * @param	string		Email address to send to. (see PHP function mail())
	 * @param	string		Subject line, non-encoded. (see PHP function mail())
	 * @param	string		Message content, non-encoded. (see PHP function mail())
	 * @param	string		Headers, separated by chr(10)
	 * @param	string		Encoding type: "base64", "quoted-printable", "8bit". Default value is "quoted-printable".
	 * @param	string		Charset used in encoding-headers (only if $encoding is set to a valid value which produces such a header)
	 * @param	boolean		If set, the header content will not be encoded.
	 * @return	boolean		True if mail was accepted for delivery, false otherwise
	 */
	public static function plainMailEncoded($email,$subject,$message,$headers='',$encoding='quoted-printable',$charset='',$dontEncodeHeader=false)	{
		if (!$charset)	{
			$charset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : 'ISO-8859-1';
		}

		$email = self::normalizeMailAddress($email);
		if (!$dontEncodeHeader)	{
				// Mail headers must be ASCII, therefore we convert the whole header to either base64 or quoted_printable
			$newHeaders=array();
			foreach (explode(chr(10),$headers) as $line)	{	// Split the header in lines and convert each line separately
				$parts = explode(': ',$line,2);	// Field tags must not be encoded
				if (count($parts)==2)	{
					if (0 == strcasecmp($parts[0], 'from')) {
						$parts[1] = self::normalizeMailAddress($parts[1]);
					}
					$parts[1] = t3lib_div::encodeHeader($parts[1],$encoding,$charset);
					$newHeaders[] = implode(': ',$parts);
				} else {
					$newHeaders[] = $line;	// Should never happen - is such a mail header valid? Anyway, just add the unchanged line...
				}
			}
			$headers = implode(chr(10),$newHeaders);
			unset($newHeaders);

			$email = t3lib_div::encodeHeader($email,$encoding,$charset);		// Email address must not be encoded, but it could be appended by a name which should be so (e.g. "Kasper Sk�rh�j <kasperYYYY@typo3.com>")
			$subject = t3lib_div::encodeHeader($subject,$encoding,$charset);
		}

		switch ((string)$encoding)	{
			case 'base64':
				$headers=trim($headers).chr(10).
				'Mime-Version: 1.0'.chr(10).
				'Content-Type: text/plain; charset="'.$charset.'"'.chr(10).
				'Content-Transfer-Encoding: base64';

				$message=trim(chunk_split(base64_encode($message.chr(10)))).chr(10);	// Adding chr(10) because I think MS outlook 2002 wants it... may be removed later again.
			break;
			case '8bit':
				$headers=trim($headers).chr(10).
				'Mime-Version: 1.0'.chr(10).
				'Content-Type: text/plain; charset='.$charset.chr(10).
				'Content-Transfer-Encoding: 8bit';
			break;
			case 'quoted-printable':
			default:
				$headers=trim($headers).chr(10).
				'Mime-Version: 1.0'.chr(10).
				'Content-Type: text/plain; charset='.$charset.chr(10).
				'Content-Transfer-Encoding: quoted-printable';

				$message=t3lib_div::quoted_printable($message);
			break;
		}

		// Headers must be separated by CRLF according to RFC 2822, not just LF.
		// But many servers (Gmail, for example) behave incorectly and want only LF.
		// So we stick to LF in all cases.
		$headers = trim(implode(chr(10), t3lib_div::trimExplode(chr(10), $headers, true)));	// Make sure no empty lines are there.


        $this->pearmail = t3lib_div::makeInstance('pearmail');
        // loading the extension configuration
        $this->pearmail->setUsername($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpUser']);
        $this->pearmail->setPassword($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpPassword']);
        $this->pearmail->setAuth(true);
        $this->pearmail->setHost($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpServer']);
        $this->pearmail->setPort($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpPort']);
        $this->pearmail->setDebug($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['stoefln_pear_mail']['smtpDebug']);

        $ret = $this->pearmail->send($to, $subject, $message, $headers);
		//$ret = @mail($email, $subject, $message, $headers);
		if (!$ret)	{
			t3lib_div::sysLog('Mail to "'.$email.'" could not be sent (Subject: "'.$subject.'").', 'Core', 3);
		}
		return $ret;
	}
}