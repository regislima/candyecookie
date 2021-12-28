<?php

use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Email\Email;
use Framework\Session\Session;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class FinalizaCompra extends Page
{
    private $venda;

    public function registraVenda()
    {
        try {
            Transaction::open();

            $cliente = Session::getValue('logged');
            $produtos = Session::getValue('carrinho');
            $valores = Session::getValue('valores');

            $this->venda = new Venda;
            $this->venda->setId_Cliente($cliente->getId());

            if ($_POST['tipo'] == 'cartao') {
                $this->venda->setId_Forma_Pagamento(1);
            }

            if ($_POST['tipo'] == 'boleto') {
                $this->venda->setId_Forma_Pagamento(2);
            }

            if ($_POST['tipo'] == 'credito') {
                $this->venda->setId_Forma_Pagamento(3);
            }
            

            $data = new DateTime();
            $this->venda->setData_Venda($data->format('d/m/Y'));
            $this->venda->setParcelas($_POST['parcelas']);
            $this->venda->setSubtotal($valores->subtotal);
            $this->venda->setDesconto($valores->desconto);
            $this->venda->setAcrescimos($valores->frete);
            $this->venda->setValor_Final($valores->total);
            $this->venda->setPresente($_POST['presente']);

            if ($produtos) {
                foreach ($produtos as $item) {
                    $this->venda->addItem($item);
                }
            }

            $this->venda->store();
            $this->updateEstoque();
            $this->updateStatus();

            if ($this->venda->getId_Forma_Pagamento() == 3) {
                $this->updateParcelas();
            }
            
            $this->sendMail();

            Transaction::close();
            
            Session::free('carrinho');
            Session::free('valores');
        } catch (Exception $e) {
            Transaction::rollback();
            new Message('error', $e->getMessage());
        }
    }

    private function sendMail()
    {
        $email = new Email;
        $anexo = $this->onGera();
        $email->message(
            'Lista de Compra',
            '<h1>Parabéns</h1><br><br>' .
            '<p>Obrigado por comprar em nossa loja. Em anexo está o arquivo com detalhes da sua compra. Você também pode acessá-lo pelo site.</p>',
            Session::getValue('logged')->getNome(),
            Session::getValue('logged')->getEmail()
        )->attach(
            $anexo, 'Compras.html'
        )->send();

        if ($email->error()) {
            new Message('error', $email->error()->getMessage());
        }

        unlink('docs/compras.html');
    }

    private function onGera()
    {
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $template = $twig->load('user_nota_compra.html');
        $replaces['model'] = $this->venda;
        $model = $template->render($replaces);

        file_put_contents('docs/compras.html', $model);
        return 'docs/compras.html';
    }

    private function updateEstoque()
    {
        if ($this->venda->getItens()) {
            foreach ($this->venda->getItens() as $item) {
                $produto = $item->getProduto();
                $produto->setEstoque($produto->getEstoque() - $item->getQuantidade());
                $produto->store('venda');
            }
        }
    }

    private function updateStatus()
    {
        $vendaStatus = new VendaStatus;
        $vendaStatus->setId_Venda($this->venda->getId());
        $vendaStatus->setId_Status(1);
        $vendaStatus->setData_Hora();
        $vendaStatus->store();
    }

    private function updateParcelas()
    {
        Conta::geraParcelas(Session::getValue('logged')->getId(), $this->venda->getId(), 30, Session::getValue('valores')->total, $_POST['parcelas']);
    }
}