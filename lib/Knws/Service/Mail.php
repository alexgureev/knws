<?php
/*
 * Mailing service
 */
namespace Knws\Service;

class Mail
{
    static protected $transport = null;
    static protected $mailer = null;
    static protected $message = null;
    static public $failed = null;

    //Knws\Service\Mail::newTransport('localhost', 25, 'user', 'pass');
    //Knws\Service\Mail::mail('w3db@yandex.ru', 'admin@knws.ru', 'test', 'attach', '/var/www/knws/composer.json', false);
    /*
    $arr = array('to' => 'w3db@yandex.ru', 'from' => 'admin@knws.ru', 'subject' => 'test', 'body' => 'array', 'attach' => null, 'send' => false);
    Knws\Service\Mail::mail($arr);
    Knws\Service\Mail::send();*/

    /**
     * newTransport create new \Swift_SmtpTransport instance
     * @param mixed $arg1
     * @param int $port smtp-service port
     * @param str $user user name to auth
     * @param str $password password to auth
     * @throws \Swift_TransportException if cant create instance
     */
    public static function newTransport($host = 'localhost', $port = 26, $user = null, $password = null)
    {
        try {
            self::$transport = \Swift_SmtpTransport::newInstance($host, $port);
        }
        catch (\Swift_TransportException $e){
            self::$failed = $e->getMessage();
        }

        if($user != null) {
            self::$transport->setUsername($user);
        }

        if($password != null) {
            self::$transport->setPassword($password);
        }
    }

    /**
     * newMessage creating \Swift_Message instance
     * @return void
     */
    protected static function newMessage()
    {
        self::$message = \Swift_Message::newInstance();
    }

    /**
     * Fill message fields with credentials
     * @param string|array $to target email or array with all credentials
     * @param string $from source email
     * @param string $subject mail subject
     * @param string $body mail body
     * @param bool $send if true, send email after filling fields
     */
    public static function mail($to, $from = null, $subject = null, $body = null, $attach = null, $send = false)
    {
        self::newMessage();
        if(is_array($to)) {
            extract($to, EXTR_OVERWRITE);
        }

        self::$message->setTo($to);
	self::$message->setSubject($subject);
	self::$message->setBody($body);
	self::$message->setFrom($from);

        if($attach!=null) {
            self::attach($attach);
        }

        if($send == true) {
            self::send();
        }
    }

    /**
     * Attach file to message
     * @param string $file file path
     */
    protected static function attach($file)
    {
        self::$message->attach(\Swift_Attachment::fromPath($file));
    }

    /**
     * Send message to smtp daemon
     * @return void
     * @throws \Swift_TransportException
     */
    public static function send()
    {
        if(!is_object(self::$transport)){
            self::newTransport();
        }

        if(self::$failed == null) {
            self::$mailer = \Swift_Mailer::newInstance(self::$transport);
        }

        try {
            self::$mailer->send(self::$message);
        }
        catch (\Swift_TransportException $e){
            self::$failed = $e->getMessage();
        }
    }
}
