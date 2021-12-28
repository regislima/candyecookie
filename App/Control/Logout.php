<?php

use Framework\Control\Page;
use Framework\Session\Session;

class Logout extends Page
{
    public function __construct()
    {
        Session::free('logged');
        header('Location: index.php');
    }
}