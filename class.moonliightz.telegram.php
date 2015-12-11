<?php

/**
 * Eine Klasse für Telegram Bot schnittstellen.
 *
 * Verfügbare Funktionen: <br>
 * sendMessage		- Nachrichten verschicken <br>
 * sendPhoto		- Bilder verschicken <br>
 * sendDocument 	- Dateien verschicken <br>
 * sendAudio		- Audios verschicken <br>
 * sendVideo		- Videos verschicken <br>
 * sendKeyboard		- Auswahlfelder einblenden <br>
 * hideKeyboard		- Auswahlfelder ausblenden <br>
 * sendChatAction	- Den Bot beispielsweise "tippt..." sagen lass (siehe Methoden beschreibung) <br>
 * setWebhook		- Webhook für den Bot setzen <br>
 * delWebhook		- Webhook löschen
 *
 * Beispiele:
 * <code>
 * <?php
 * require_once('class.moonliightz.telegram.php');
 *
 * $bot = new Telegram(BOT KEY);
 *
 * $bot->sendMessage(CHAT_ID, "Text");
 * $bot->sendPhoto(CHAT_ID, "storageplan.png", "Bildunterschrift");
 * $bot->sendDocument(CHAT_ID, "storageplan.png");
 * $bot->sendAudio(CHAT_ID, "BVB.mp3", "Interpret", "Titel");
 *
 * $bot->sendKeyboard(CHAT_ID, "Text", array( array( "Zeile1 Test1", "Zeile1 Test2" ), array( "Zeile2 Test3", "Zeile2 Test4" ) ));
 * $bot->hideKeyboard(CHAT_ID, "Text");
 *
 * $bot->sendChatAction(CHAT_ID, 1);
 *
 * $bot->setWebhook(URL);
 * $bot->delWebhook();
 * ?>
 * </code>
 *
 * @author      MoonLiightz <info@moonliightz.de>
 * @category	Telegram Bot
 * @link		https://moonliightz.de/produkt/php-telegram-klasse/
 * @version		0.79 [Beta Version]
 * @since		21.10.2015
 */

class Telegram
{
	
	/**
	* Telegram Bot ID
	*
	* @var    string
	* @access private
	*/
	private $bot_key;
	
	
	/**
	* Konstruktor => Setzt Bot ID 
	*
	* @param  string  $bkey Bot ID
	* @access public
	*/
	public function __construct($bkey = NULL)
	{
		$this->bot_key = $bkey;
	}
	
	/**
	* Anfrage an Telegram senden
	*
	* @param	string	$action
	* @param	array	$data
	* @return	array
	* @access	private
	*/
	private function send($action, $data = array()) 
	{
		$apiendpoint = ucfirst($action);

		$ch = curl_init("https://api.telegram.org/bot".$this->bot_key."/".$apiendpoint);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HEADER => false,
			CURLOPT_HTTPHEADER => array(
				'Host: api.telegram.org',
				'Content-Type: multipart/form-data'
			),
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_CONNECTTIMEOUT => 6000,
			CURLOPT_SSL_VERIFYPEER => false
		));
		$result = curl_exec($ch);
		curl_close($ch);
		
		return !empty($result) ? json_decode($result, true) : false;
	}
	
	/**
	* Nachricht senden
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$chat_id	required	ID des Telegram Chats
	* @param	string	$text		required	Text der gesendet werden soll
	* @param	boolean	$preview	optinal		Legt fest ob Webpreview deaktivert werden soll
	* @return	array
	* @access public
	*/
	public function sendMessage($chat_id, $text, $preview = false)
	{
		$action = 'sendMessage';
		$param = array(
			'chat_id'					=>	$chat_id,
			'text'						=>	$text,
			'disable_web_page_preview'	=>	$preview
		);
		
		$res = $this->send($action, $param);
		if (!$res['ok'])
			$result = Array("success" => 0, "info"	=>	"Error: " . $res['description']);
		else
			$result = Array("success" => 1,	"info"	=>	"Message send");
		
		return $result;
	}
	
	/**
	* Bild senden
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$chat_id	required	ID des Telegram Chats
	* @param	string	$photo		required	Bild das gesendet werden soll
	* @param	string	$caption	optional	Bildbeschreibung
	* @return	array
	* @access	public
	*/
	public function sendPhoto($chat_id, $photo, $caption = NULL)
	{
		$action = 'sendPhoto';
		$param = array(
			'chat_id'	=>	$chat_id,
			'photo'		=>	$this->curlFile($photo),
			'caption'	=>	$caption
		);
		
		$res = $this->send($action, $param);
		if (!$res['ok'])
			$result = Array("success" => 0, "info"	=>	"Error: " . $res['description']);
		else
			$result = Array("success" => 1,	"info"	=>	"Photo send");
		
		return $result;
	}
	
	/**
	* Dateien senden
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$chat_id	required	ID des Telegram Chats
	* @param	string	$document	required	Datei die gesendet werden soll
	* @return	array
	* @access	public
	*/
	public function sendDocument($chat_id, $document)
	{	
		$action = 'sendDocument';
		$param = array(
			'chat_id'	=>	$chat_id,
			'document'	=>	$this->curlFile($document)
		);
		
		$res = $this->send($action, $param);
		if (!$res['ok'])
			$result = Array("success" => 0, "info"	=>	"Error: " . $res['description']);
		else
			$result = Array("success" => 1,	"info"	=>	"Document send");
		
		return $result;
	}
	
	/**
	* Audio senden
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$chat_id	required	ID des Telegram Chats
	* @param	string	$audio		required	Audio Datei die gesendet werden soll
	* @param	string	$interpret	optional	Interpret
	* @param	string	$title		optional	Titel
	* @return	array
	* @access	public
	*/
	public function sendAudio($chat_id, $audio, $interpret = NULL, $title = NULL)
	{	
		$action = 'sendAudio';
		$param = array(
			'chat_id'	=>	$chat_id,
			'audio'		=>	$this->curlFile($audio),
			'performer'	=>	$interpret,
			'title'		=>	$title
		);
		
		$res = $this->send($action, $param);
		if (!$res['ok'])
			$result = Array("success" => 0, "info"	=>	"Error: " . $res['description']);
		else
			$result = Array("success" => 1,	"info"	=>	"Audio send");
		
		return $result;
	}
	
	/**
	* Video senden
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$chat_id	required	ID des Telegram Chats
	* @param	string	$video		required	Viedeo das gesendet werden soll
	* @param	string	$caption	optional	Videobeschreibung
	* @return	array
	* @access	public
	*/
	public function sendVideo($chat_id, $video, $caption = NULL)
	{
		$action = 'sendPhoto';
		$param = array(
			'chat_id'	=>	$chat_id,
			'video'		=>	$this->curlFile($video),
			'caption'	=>	$caption
		);
		
		$res = $this->send($action, $param);
		if (!$res['ok'])
			$result = Array("success" => 0, "info"	=>	"Error: " . $res['description']);
		else
			$result = Array("success" => 1,	"info"	=>	"Video send");
		
		return $result;
	}
	
	/**
	* Chat Aktion senden
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$chat_id	required	ID des Telegram Chats
	* @param	integer	$type		required	1 => Nachrichten, 2 => Fotos, 3 => Viedeo aufnehmen, 4 => Viedeo senden/hochladen, 5 => Audio aufnehmen, 6 => Audio senden/hochladen, 7 => Dateien
	* @return	array
	* @access	public
	*/
	public function sendChatAction($chat_id, $type)
	{
		$do_action = "";
		
		switch($type)
		{
			case 1:
				$do_action = "typing";
			break;
			
			case 2:
				$do_action = "upload_photo";
			break;
			
			case 3:
				$do_action = "record_video";
			break;
			
			case 4:
				$do_action = "upload_video";
			break;
			
			case 5:
				$do_action = "record_audio";
			break;
			
			case 6:
				$do_action = "upload_audio";
			break;
			
			case 7:
				$do_action = "upload_document";
			break;
		}
		
		$action = 'sendChatAction';
		$param = array(
			'chat_id'	=>	$chat_id,
			'action'	=>	$do_action
		);
		
		$res = $this->send($action, $param);
		if (!$res['ok'])
			$result = Array("success" => 0, "info"	=>	"Error: " . $res['description']);
		else
			$result = Array("success" => 1,	"info"	=>	"Chat Action send");
		
		return $result;
	}
	
	/**
	* Auswahl Keyboard zeigen
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$chat_id	required	ID des Telegram Chats
	* @param	string	$text		required	Text der gesendet werden soll
	* @param	array	$keyboard	required	Auswahlfelder z.B. array( array( "Zeile1 Test1", "Zeile1 Test2" ), array( "Zeile2 Test3", "Zeile2 Test4" ) )
	* @return	array
	* @access	public
	*/
	public function sendKeyboard($chat_id, $text, $keyboard = Array())
	{
		$action = 'sendMessage';
		$param = array(
			'chat_id'		=>	$chat_id,
			'reply_markup'	=>	json_encode(array("keyboard" => $keyboard)),
			'text'			=>	$text
		);
		
		$res = $this->send($action, $param);
		if (!$res['ok'])
			$result = Array("success" => 0, "info"	=>	"Error: " . $res['description']);
		else
			$result = Array("success" => 1,	"info"	=>	"Keyboard show");
		
		return $result;
	}
	
	/**
	* Auswahl Keyboard ausblenden
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$chat_id	required	ID des Telegram Chats
	* @param	string	$text		required	Text der gesendet werden soll
	* @return	array
	* @access	public
	*/
	public function hideKeyboard($chat_id, $text)
	{
		$action = 'sendMessage';
		$param = array(
			'chat_id'		=>	$chat_id,
			'reply_markup'	=>	json_encode(array("hide_keyboard" => true)),
			'text'			=>	$text
		);
		
		$res = $this->send($action, $param);
		if (!$res['ok'])
			$result = Array("success" => 0, "info"	=>	"Error: " . $res['description']);
		else
			$result = Array("success" => 1,	"info"	=>	"Keyboard hide");
		
		return $result;
	}
	
	/**
	* Webhook setzen
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @param	string	$url	required	URL zu der Datei mit der der Telegram Bot verbunden werden soll
	* @return	array
	* @access	public
	*/
	public function setWebhook($url = NULL) 
	{
		$result = Array();
		
		if (empty($url))
		  $result = Array("success" => 0, "info" => "Keine gültige URL angegeben");
		else 
		{
			$url .= "?sender=telegram";
			$res = $this->send('setWebhook', array('url' => $url));
			if (!$res['ok'])
				$result = Array("success" => 0, "info" => "Webhook was not set! Error: " . $res['description']);
			else
				$result = Array("success" => 1, "info"	=>	$res['description']);
		}
		
		return $result;
	}
	
	/**
	* Webhook löschen
	*
	* <b>Output:</b><br>
	* <code>
	*  Array
	*  (
	*      [success] => 1 oder 0
	*      [info]	=> Zeigt Info oder Fehlermeldung	
	*  )
	* </code>
	*
	* @return	array
	* @access	public
	*/
	public function delWebhook() 
	{
		$result = Array();
		
		$res = $this->send('setWebhook');
		if (!$res['ok'])
			$result = Array("success" => 0, "info" => "Webhook was not delete! Error: " . $res['description']);
		else
			$result = Array("success" => 1, "info"	=>	$res['description']);
		
		
		return $result;
	}
	
	/**
	* create curl file
	*
	* @param string $fileName
	* @return string
	*/
	private function curlFile($fileName) 
	{
		$filename = realpath($fileName);
		
		if (!is_file($filename))
			throw new Exception('File does not exists');
		
		if (function_exists('curl_file_create'))
			return curl_file_create($filename);

		return "@$filename";
	}
}

?>