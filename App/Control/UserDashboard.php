<?php

use Framework\Control\Page;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Email\Email;
use Framework\Session\Session;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class UserDashBoard extends Page
{
    private $replaces;

    public function __construct()
    {
        $this->replaces = [];

        if (Session::getValue('logged')) {
            $loader = new FilesystemLoader('App/Resources/Templates');
            $twig = new Environment($loader);
            $template = $twig->load('user_dashboard.html');

            Transaction::open();   
            
            $criteria = new Criteria;
            $criteria->add('id_cliente', '=', Session::getValue('logged')->getId());
            $repository = new Repository('Venda');
            $this->replaces['pedidos'] = $repository->load($criteria);
            $this->replaces['pessoa'] = Pessoa::find(Session::getValue('logged')->getId());
            $this->replaces['usados'] = Conta::debitosPorPessoa(Session::getValue('logged')->getId());

            $repository = new Repository('Conta');
            $this->replaces['contas'] = $repository->load($criteria);
            parent::add($template->render($this->replaces));
            
            Transaction::close();
        }
    }

    public function contact()
    {
        try {
            Transaction::open();

            $contato = new Contato;
            $contato->setId_Cliente(Session::getValue('logged')->getId());
            $contato->setAssunto($_POST['assunto']);
            $contato->setMensagem($_POST['mensagem']);
            $contato->setData_Hora();
            $contato->store();
            new Message('info', 'Mensagem enviada. Logo logo retonaremos o contato no seu email.');
            
            Transaction::close();
        } catch (Exception $e) {
            Transaction::rollback();
            new Message('error', 'Falha ao enviar a mensagem: ' . $e->getMessage());
        }
    }
}