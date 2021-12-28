<?php

use Framework\Control\Page;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Session\Session;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class LoginForm extends Page
{
    private $replaces;
    private $template;
    private $header;

    public function __construct()
    {
        $this->replaces = [];

        if (Session::getValue('logged')) {
            $this->header = 'Location: index.php?class=Checkout';
        } else {
            $loader = new FilesystemLoader('App/Resources/Templates');
            $twig = new Environment($loader);
            $this->template = $twig->load('login_form.html');
        }
    }

    public function autentication()
    {
        if ($_POST) {
            try {
                Transaction::open();

                $criteria = new Criteria;
                $criteria->add('email', '=', $_POST['user']);
                $criteria->add('cpf', '=', $_POST['user'], 'or');

                $repository = new Repository('Pessoa');
                $pessoa = $repository->load($criteria);

                if ($pessoa) {
                    if ($pessoa[0]->getSenha() == crypt($_POST['senha'], $pessoa[0]->getSenha())) {
                        Session::setValue('logged', $pessoa[0]);

                        if (isset($_POST['to'])) {
                            header('Location: index.php?class=Checkout');
                        } else {
                            header('Location: index.php');
                        }
                        
                    } else {
                        $info = new Message('info', 'Credenciais incorretas.');
                        $this->replaces['info'] = $info;
                    }
                } else {
                    $info = new Message('info', 'Credenciais incorretas.');
                    $this->replaces['info'] = $info;
                }

                Transaction::close();
            } catch (Exception $e) {
                $erro = new Message('error', $e->getMessage());
                $this->replaces['info'] = $erro;
            }
        }
    }

    public function show()
    {
        if ($this->template) {
            parent::add($this->template->render($this->replaces));
            parent::show();
        } else {
            header($this->header);
        }
    }
}