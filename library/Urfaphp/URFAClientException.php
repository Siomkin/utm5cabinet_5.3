<?php
/**
 * Специальный класс исключения для секюрности
 * (если применять стандартный класс Exception, то выводится бэктрэйс в котором 
 * можно увидеть пароли подключения в БД, биллингу и др. критическую инфу)
 */
class Urfaphp_URFAClientException extends Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . " [code: {$this->code}]: {$this->message}\n";
    }
}
 ?>