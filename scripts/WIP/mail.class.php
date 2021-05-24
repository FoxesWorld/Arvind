<?php
/*
=====================================================
 FoxesWorld - by ArcJet Systems
-----------------------------------------------------
 https://foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020 ArcJet Systems
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: mail.class.php
-----------------------------------------------------
 Назначение: Класс для отправки писем с сайта
=====================================================
*/
/*
 * Using
 * $mail = new foxMail($config,1);
 * $mail->send(self::$email, "FoxEngine", self::$template);
*/
if (!defined('INCLUDE_CHECK')) {
	die ("Hacking attempt!");
}

require_once ('class.mailer.php');

class ArvindMail {

	public $mail = false;
	public $send_error = false;
	public $smtp_msg = "";
	public $from = false;
	public $html_mail = false;
	public $bcc = array ();
	public $keepalive = false;
	
	function __construct($config, $is_html = false) {
		
		$this->mail = new PHPMailer;
		$this->mail->CharSet = $config['charset'];
		$this->mail->Encoding = "base64";

		$config['letterHeadLine'] = str_replace( '&amp;', '&', $config['letterHeadLine'] );

		if( $config['letterHeadLine'] ) {
			$this->mail->setFrom($config['adminEmail'], $config['letterHeadLine']);
		} else {
			$this->mail->setFrom( $config['adminEmail'] );			
		}
		
		if($config['sendMethod'] == "smtp") {
			$this->mail->isSMTP();
			$this->mail->Timeout = 10;
			$this->mail->Host = $config['sendHost'];
			$this->mail->Port = intval( $config['SMTPport'] );
			$this->mail->SMTPSecure = $config['SMTPsecProtocol'];
			
			if( $config['smtp_user'] ) {
				$this->mail->SMTPAuth = true;
				$this->mail->Username = $config['smtp_user'];
				$this->mail->Password = $config['SMTPpass'];
			}
			
			if( $config['SMTPMail'] ) {
				$this->mail->From = $config['SMTPMail'];
				$this->mail->Sender = $config['SMTPMail'];
			}
		}
		
		$this->mail->XMailer = "FoxesWorld | Arvind";
		
		if ( $is_html ) {
			$this->mail->isHTML();
			$this->html_mail = true;
		}
	}
	
	function send($to, $subject, $message) {
		
		if( $this->from ) {
			$this->mail->addReplyTo($this->from, $this->from);
		}
		
		$this->mail->addAddress($to);
		$this->mail->Subject = $subject;
		
		if($this->mail->Mailer == 'smtp' AND $this->keepalive ) {
			$this->mail->SMTPKeepAlive = true;
		}
		
		if( $this->html_mail ) {
			$this->mail->msgHTML($message);
		} else {
			$this->mail->Body = $message;
		}

		if( count( $this->bcc ) ) {
			
			foreach($this->bcc as $bcc) {
				$this->mail->addBCC($bcc);
			}
			
		}
		
		if (!$this->mail->send()) {
			$this->smtp_msg = $this->mail->ErrorInfo;
			$this->send_error = true;
		}
		
		$this->mail->clearAllRecipients();
		$this->mail->clearAttachments();
	
	}
	
	function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment') {
		$this->mail->addAttachment( $path, $name, $encoding, $type, $disposition );
	}
}
?>