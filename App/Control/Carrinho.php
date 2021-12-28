<?php

use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Session\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Carrinho extends Page
{
    private $replaces;

    public function __construct()
    {
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $template = $twig->load('carrinho.html');
        
        $this->replaces['produtos'] = Session::getValue('carrinho');
        $this->calculaTotal();

        parent::add($template->render($this->replaces));
    }

    public function onDelete()
    {
        $list = Session::getValue('carrinho');
        unset($list[$_GET['id']]);
        Session::setValue('carrinho', $list);
    }

    public function calculaTotal()
    {
        $valores = new stdClass;
        $valores->subtotal = 0;
        $valores->frete = 0;
        $valores->taxa = 0;

        // Verificando se já teve um desconto
        if (Session::getValue('valores')) {
            $valores->desconto = Session::getValue('valores')->desconto;
        } else {
            $valores->desconto = 0;
        }
        
        $list = Session::getValue('carrinho');

        // Verifica se foi alterada a quantidade de algum produto no carrinho
        if (isset($_POST['id']) and isset($_POST['qtd'])) {
            $list[$_POST['id']]->quantidade = $_POST['qtd'];
            Session::setValue('carrinho', $list);
        }

        if ($list) {
            foreach ($list as $produto) {
                $valores->subtotal += ($produto->preco_venda * $produto->quantidade);
            }
        }

        Transaction::open();
        // Recupera o valor do frete cadastrado
        $valor_frete = Config::all()[0]->getValor_Frete();

        // Recupera o valor de gasto mínimo
        $valor_minimo = Config::all()[0]->getValor_Minimo_Frete();
        Transaction::close();

        if ($valores->subtotal < $valor_minimo and $list) {
            $valores->frete = $valor_frete;
        }

        // Verifica se foi aplicado algum desconto
        if (isset($_POST['desc'])) {

            // Verifica se o desconto informado é válido
            $desc = number_format(floatval($_POST['desc']), 2, '.', ',');

            if ($desc >= 0 && $desc <= $valores->subtotal) {
                $valores->desconto = $desc;
            }
        }
        
        $valores->total = (($valores->subtotal + $valores->frete + $valores->taxa) - $valores->desconto);
        $this->replaces['valores'] = $valores;
        Session::setValue('valores', $valores);
    }
}