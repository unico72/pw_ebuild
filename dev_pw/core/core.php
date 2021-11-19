<?php

require 'mail/PHPMailerAutoload.php';

Class Registration {
	
	protected $Config;
	
	Function __construct($config) {
		$this->Config = $config;
	}
	
	Function CheckAccountField($login, $email, $pass, $repass) {
		$mysql = mysql_connect($this->Config['mysql_host'], $this->Config['mysql_user'], $this->Config['mysql_pass']);
		$mysql = mysql_select_db($this->Config['mysql_db']);
		if(empty($login) or empty($email) or empty($pass) or empty($repass)) {
			return 0;
		} elseif (ereg("[^0-9a-zA-Z_-]", $login, $unk)) {
			return 0;
		} elseif (ereg("[^0-9a-zA-Z_-]", $pass, $unk)) {
			return 0;
		} elseif (ereg("[^0-9a-zA-Z_-]", $repass, $unk)) {
			return 0;
		} elseif ($pass != $repass) {
			return 3;
		} elseif ((strlen($login) < $this->Config['LoginMinLeng']) or (strlen($login) > $this->Config['LoginMaxLeng'])) {
			return 1;
		} elseif ((strlen($pass) < $this->Config['PassMinLeng']) or (strlen($pass) > $this->Config['PassMaxLeng'])) {
			return 2;
		} elseif ((strlen($email) < $this->Config['EmailMinLeng']) or (strlen($email) > $this->Config['EmailMaxLeng'])) {
			return 4;
		} else {
			$Q = mysql_query("SELECT * FROM users WHERE name='$login'");
			if (mysql_num_rows($Q)) {
				return 5;
			}
			$Q = mysql_query("SELECT * FROM users WHERE email='$email'");
			if (mysql_num_rows($Q)) {
				return 6;
			} else {
				return 7;
			}
		}
		mysql_close();
	}
	
	Function AddAccount($login, $email, $pass) {
		$mysql = mysql_connect($this->Config['mysql_host'], $this->Config['mysql_user'], $this->Config['mysql_pass']);
		$mysql = mysql_select_db($this->Config['mysql_db']);
		$hash = $this->HashPassword($pass, $login);
		if (!$this->Config['RequireEmailConfirmation']) {
			mysql_query("call adduser('$login', '$hash', '0', '0', '0', '0', '$email', '0', '0', '0', '0', '0', '0', '0', '', '', '$hash')") or die (mysql_error());
			if ($this->Config['GiveGold']) {
				$this->GiveGold($login);
			}
		} else {
			$hash = strrev($hash);
			$key = $this->randString();
			mysql_query("call adduser('$login', '$hash', '0', '0', '0', '0', '$email', '0', '0', '0', '0', '0', '0', '0', '', '$key', '$hash')") or die (mysql_error());
			$this->SendSMTPMail($email, $key, 0);
		}
		mysql_close();
	}
	
	Function ActiveAccount($key) {
		$mysql = mysql_connect($this->Config['mysql_host'], $this->Config['mysql_user'], $this->Config['mysql_pass']);
		$mysql = mysql_select_db($this->Config['mysql_db']);
		$Q = mysql_query("SELECT * FROM users WHERE qq='$key'");
		if (!mysql_num_rows($Q)) {
			return false;
		} else {
			$Q = mysql_fetch_array($Q);
			$pass = strrev($Q['passwd']);
			mysql_query("UPDATE users SET passwd='$pass', qq='', passwd2='$pass' WHERE name='$Q[name]'") or die (mysql_error());
			mysql_close();
			if ($this->Config['GiveGold']) {
				$this->GiveGold($Q['name']);
			}
			return true;
		}
	}
	
	Function HashPassword($pass, $login) {
		switch($this->Config['PasswordHash']) {
			case 0:
				return "0x".md5($login.$pass);
				break;
			case 1:
				return base64_encode(md5($login.$pass, true));
				break;
			default:
				return false;
				break;
		}
	}
	
	Function randString() {
		$str = "";
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
		$size = strlen($chars);
		for( $i = 0; $i < 15; $i++ ) {
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}
		return $str;
	}
	
	Function SendSMTPMail($to, $key, $type) {
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->Host = $this->Config['SMTP_HOST'];
		$mail->SMTPAuth = true;
		$mail->Username = $this->Config['SMTP_LOGIN'];
		$mail->Password = $this->Config['SMTP_PASS'];
		$mail->SMTPSecure = $this->Config['SMTP_HASH'];
		$mail->Port = $this->Config['SMTP_PORT'];
		$mail->From = $this->Config['SMTP_LOGIN'];
		$mail->FromName = $this->Config['SMTP_FROM'];
		$mail->addAddress($to);
		$mail->isHTML(true);
		switch($type) {
			case 0: // рега
				$link = $this->Config['RegUrl']."index.php?key=".$key;
				$subj = "Подтверждение регистрации на сервере {$this->Config['ServerName']}";
				$text = "Для подтверждения регистрации на сервере {$this->Config['ServerName']} перейдите по ссылке: <a href='{$link}' target='_blank'>{$link}</a>";
				break;
			case 1: // восстановление
				$link = $this->Config['RegUrl']."index.php?mod=newpass&rkey=".$key;
				$subj = "Восстановление пароля на сервере {$this->Config['ServerName']}";
				$text = "Для восстановления пароля на сервере {$this->Config['ServerName']} перейдите по ссылке: <a href='{$link}' target='_blank'>{$link}</a>";
				break;
		}
		$mail->Subject = $subj;
		$mail->Body = $text;
		$mail->AltBody = '';
		if(!$mail->send()) {
			return false;
		} else {
			return true;
		}
	}
	
	Function GiveGold($name) {
		$gold = $this->Config['GoldCount'];
		$mysql = mysql_connect($this->Config['mysql_host'], $this->Config['mysql_user'], $this->Config['mysql_pass']);
		$mysql = mysql_select_db($this->Config['mysql_db']);
		$q = mysql_query("SELECT * FROM users WHERE name='$name'");
		$q = mysql_fetch_array($q);
		mysql_query("call usecash('$q[ID]', 1 , 0, 1, 0, '$gold', 1, @error)");
		mysql_close();
	}
	
	Function RestorePass_Add($email) {
		if (!empty($email)) {
		$mysql = mysql_connect($this->Config['mysql_host'], $this->Config['mysql_user'], $this->Config['mysql_pass']);
		$mysql = mysql_select_db($this->Config['mysql_db']);
		$q = mysql_query("SELECT * FROM users WHERE email='$email'");
		if (!mysql_num_rows($q)) {
			return 0;
		} else {
			$key = $this->randString();
			$q = mysql_query("UPDATE users SET address='$key' WHERE email='$email'");
			$this->SendSMTPMail($email, $key, 1);
		}
		mysql_close();
		return 1;
		} else return 0;
	}
	
	Function RestorePass($key, $pass, $repass) {
		if (empty($pass) or empty($repass)) {
			return 1;
		} elseif ((strlen($pass) < $this->Config['PassMinLeng']) or (strlen($pass) > $this->Config['PassMaxLeng'])) {
			return 3;
		}
		$mysql = mysql_connect($this->Config['mysql_host'], $this->Config['mysql_user'], $this->Config['mysql_pass']);
		$mysql = mysql_select_db($this->Config['mysql_db']);
		$q = mysql_query("SELECT * FROM users WHERE address='$key'");
		if(!mysql_num_rows($q)) {
			return 0;
		} else {
			$q = mysql_fetch_array($q);
			$pass = $this->HashPassword($pass, $q['name']);	
			mysql_query("UPDATE users SET passwd='$pass', address='', passwd2='$pass' WHERE name='$q[name]'") or die (mysql_error());
			return 2;
		}
		mysql_close();
	}
	
}
?>