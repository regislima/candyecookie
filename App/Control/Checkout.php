<?php

use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Session\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Checkout extends Page
{
    private $replaces = [];

    public function __construct()
    {
        Transaction::open();

        $pessoa = Session::getValue('logged');
        $valores = Session::getValue('valores');
        $produtos = Session::getValue('carrinho');
        
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $template = $twig->load('checkout.html');
        
        if ($pessoa and $valores and $produtos) {
            $this->replaces['produtos'] = $produtos;
            $this->replaces['valores'] = $valores;
            $this->replaces['pessoa'] = $pessoa;
            $this->replaces['restante'] = $pessoa->getCredito() - Conta::debitosPorPessoa($pessoa->getId());
        }

        parent::add($template->render($this->replaces));

        Transaction::close();
    }
}