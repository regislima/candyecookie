<?php

namespace Framework\Session;

class Session {

    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    static public function setValue($var, $value)
    {
        $_SESSION[$var] = $value;
    }

    static public function getValue($var)
    {
        if (isset($_SESSION[$var])) {
            return $_SESSION[$var];
        }
    }

    static public function freeSession()
    {
        $_SESSION = [];
        session_destroy();
    }

    static public function free($var) {
        unset($_SESSION[$var]);
    }
}