<?php namespace Knws\Service;

class Mail
{
    static protected $transport = null;
    static protected $mailer = null;
    static protected $message = null;
    static public $failed = null;

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
            self::$transport = Swift_SmtpTransport::newInstance($host, $port);
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
        self::$message = Swift_Message::newInstance();
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
        var_dump(self::$transport);
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
     * Attach file to email instance
     * @param string $file file path
     */
    protected static function attach($file)
    {
        self::$message->attach(Swift_Attachment::fromPath($file));
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
            self::$mailer = Swift_Mailer::newInstance(self::$transport);
        }

        try {
            self::$mailer->send(self::$message);
        }
        catch (Swift_TransportException $e){
            self::$failed = $e->getMessage();
        }
    }
}
