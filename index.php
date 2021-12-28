<?php

use Framework\Core\AppLoader;
use Framework\Core\ClassLoader;
use Framework\Database\Transaction;
use Framework\Session\Session;

require_once 'Lib/Framework/Core/ClassLoader.php';
require_once 'Lib/Framework/Core/AppLoader.php';

date_default_timezone_set('America/Fortaleza');

$al = new ClassLoader;
$al->addNamespace('Framework', 'Lib/Framework');
$al->register();

$al = new AppLoader;
$al->addDirectory('App/Control');
$al->addDirectory('App/Model');
$al->addDirectory('App/Helper');
$al->register();

# Carrega bibliotecas de terceiros
if (file_exists('vendor')) {
    $loader = require 'vendor/autoload.php';
    $loader->register();
}

$content = '';
$output = '';

new Session;

Transaction::open();

$user = Session::getValue('logged');
$class = 'LojaIndex';

if ($user) {
    if ($user->getGrupo()->getNome() == 'Administrador') {
        $template = file_get_contents('App/Resources/Templates/admin/admin_template.html');
        $menu = $user->getNome();

        // Verifica se tem algum contato não lido
        $contato = 0;
        if (IndexHelper::ContatoNaoLido()) {
            $contato = count(IndexHelper::ContatoNaoLido());
        }

        // Verifica as vendas com o status de 'recebido'
        $vendas = "<a class='dropdown-item' href='#'>0 Pedidos Recebidos(s)</a>";;
        $qtdVendas = 0;
        if (IndexHelper::VendasRecebidas()) {
            $vendas = '';
            foreach (IndexHelper::VendasRecebidas() as $value) {
                $vendas .= "<a class='dropdown-item' href='?class=VendaDetails&method=onDetail&key={$value->getId_Venda()}&id={$value->getId_Venda()}'>Pedido Recebido: {$value->getData_hora()}</a>";
            }

            $qtdVendas = count(IndexHelper::VendasRecebidas());
        }
        
        $class = 'Dashboard';
    }

    if ($user->getGrupo()->getNome() == 'Cliente') {
        $template = file_get_contents('App/Resources/Templates/template_loja.html');
        $menu = file_get_contents('App/Resources/Templates/menu_user.html');
        $nome_user = $user->getNome();
        $menu = str_replace('{{nome_user}}', $nome_user, $menu);
    }

    $imagem = $user->getImagem() ? $user->getImagem() : 'user_no_image.png';
} else {
    $template = file_get_contents('App/Resources/Templates/template_loja.html');
    $menu = file_get_contents('App/Resources/Templates/menu_no_logged.html');
}

Transaction::close();

if (isset($_GET['class'])) {
    $class = $_GET['class'];
}

if (class_exists($class)) {
    try {
        $pagina = new $class;
        ob_start();
        $pagina->show();
        $content = ob_get_contents();
        ob_end_clean();
    } catch (Exception $e) {
        $content = $e->getMessage() . '<br>' . $e->getTraceAsString();
    }
} else {
    $content = "Página <b>{$class}</b> não encontrada.";
}

//Verificando se o carrinho tem itens
$carrinho = 0;
if (Session::getValue('carrinho') != null) {
    $carrinho = count(Session::getValue('carrinho'));
}

$output = str_replace('{{content}}', $content, $template);

if ($user and ($user->getGrupo()->getNome() == 'Administrador')) {
    $output = str_replace('{{qtd_contatos}}', $contato, $output);
    $output = str_replace('{{qtd_vendas_recebidas}}', $qtdVendas, $output);
    $output = str_replace('{{vendas_recebidas}}', $vendas, $output);
    $output = str_replace('{{imagem}}', $imagem, $output);
}

if (is_null($user) or ($user->getGrupo()->getNome() == 'Cliente')) {
    $output = str_replace('{{carrinho}}', $carrinho, $output);
}

if (isset($menu)) {
    $output = str_replace('{{menu}}', $menu, $output);
}

echo $output;