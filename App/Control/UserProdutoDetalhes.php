<?php

use Framework\Control\Page;
use Framework\Database\Transaction;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class UserProdutoDetalhes extends Page
{
    private $replaces;

    public function __construct()
    {
        $this->activeRecord = 'Produto';
        
        Transaction::open();
        $this->replaces['produto'] = Produto::find($_GET['produto_id']);

        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $template = $twig->load('user_produto_detalhes.html');
        
        parent::add($template->render($this->replaces));
        Transaction::close();
    }
}