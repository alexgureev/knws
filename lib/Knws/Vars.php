<?php namespace Knws;

class Vars
{
    public $vars;

    public function __construct()
    {
        //TODO  http://www.php.net/manual/en/filter.filters.validate.php
        /*
        $email = filter_var($input, FILTER_SANITIZE_EMAIL);
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            echo "Your email '$email' was accepted";
        } else {
            echo "Sorry, but '$email' seems not to be an email";
        }
*/
        //1
        $this->vars = $_REQUEST; //1
    }

    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function &__get($name)
    {
        return $this->vars[$name];
    }

    public function test()
    {
        //print_r(func_get_args());
    }
}
?>
