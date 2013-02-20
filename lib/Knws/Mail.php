<?php namespace Knws;

class Mail
{
    public $transport;
    public $mailer;
    public $message;
    public $failed;

    public function __construct()
    {
        //require_once LIB_PATH.'/Swift/swift_required.php';
        return $this;
    }

    public function newTransport($host = 'localhost', $port = 25, $user = NULL, $password = NULL)
    {
        $this->transport = \Swift_SmtpTransport::newInstance($host, $port);
        if($user != NULL)
	$this->transport->setUsername($user);
        if($password != NULL)
	$this->transport->setPassword($password);
        return $this;
    }

    public function newMessage()
    {
        $this->message = \Swift_Message::newInstance();
        return $this;
    }

    public function mail($to, $from, $subject, $body)
    {
        if(!is_object($this->message))
        {
            $this->newMessage();
        }

        $this->message->setTo($to);
	$this->message->setSubject($subject);
	$this->message->setBody($body);
	$this->message->setFrom($from);
        return $this;
    }

    public function attach($file)
    {
        if(!is_object($this->message))
        {
            $this->newMessage();
        }

        $this->message->attach(\Swift_Attachment::fromPath($file));
        return $this;
    }

    public function send()
    {
        if(!is_object($this->transport))
        {
            $this->newTransport();
        }

	$this->mailer = \Swift_Mailer::newInstance($this->transport);
	$this->mailer->send($this->message);
    }
}

?>
