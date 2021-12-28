<?php

use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Session\Session;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class UserSucesso extends Page
{
    private $activeRecord;
    private $template;

    public function __construct()
    {
        $this->activeRecord = 'Pessoa';
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $this->template = $twig->load('user_sucesso.html');

        try {
            Transaction::open();
            $class = $this->activeRecord;
            $object = $class::find(Session::getValue('logged')->getId());
            $replaces['model'] = $object;
            $model = $this->template->render($replaces);
            Transaction::close();
            parent::add($model);
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }
}