<?php

use Framework\Control\Page;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Traits\DeleteTrait;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ContaCreditoDetails extends Page
{
    private $activeRecord;
    private $template;
    private $loaded;

    use DeleteTrait { onDelete as onDeleteTrait; }

    public function __construct()
    {
        $this->activeRecord = 'Conta';
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $this->template = $twig->load('admin/conta_credito_detalhes.html');
    }

    public function onReload()
    {
        $criteria = new Criteria;

        if (isset($_POST['dt_emissao']) and !empty($_POST['dt_emissao'])) {
            $criteria->add('dt_vencimento', '>=', $_POST['dt_emissao']);
        }

        if (isset($_POST['dt_vencimento']) and !empty($_POST['dt_vencimento'])) {
            $criteria->add('dt_vencimento', '<=', $_POST['dt_vencimento']);
        }

        if (isset($_GET['id_cliente'])) {
            $id_cliente = $_GET['id_cliente'];
        }

        if (isset($_POST['id_cliente'])) {
            $id_cliente = $_POST['id_cliente'];
        }

        Transaction::open();

        $replaces['cliente'] = Pessoa::find($id_cliente);

        $criteria->add('id_cliente', '=', $id_cliente);
        $repository = new Repository('Conta');
        $replaces['contas'] = $repository->load($criteria);
        $replaces['debitos'] = Conta::debitosPorPessoa($id_cliente);
        
        parent::add($this->template->render($replaces));

        Transaction::close();

        $this->loaded = true;
    }

    public function onEdit()
    {
        if ($_POST) {
            try {
                Transaction::open();

                $conta = Conta::find($_POST['id']);

                if (!empty($_POST['dataVencimentoNovo'])) {
                    $conta->setDt_Vencimento($_POST['dataVencimentoNovo']);
                }
                
                if ($_POST['paga'] == 'false') {
                    $paga = 'N';
                } else {
                    $paga = 'S';
                }

                $conta->setPaga($paga);
                $conta->store();

                Transaction::close();
            } catch (Exception $e) {
                Transaction::rollback();
            }
        }
    }

    public function addCredito()
    {
        if (isset($_POST['id_cliente']) and isset($_POST['credito'])) {
            Transaction::open();

            $cliente = Pessoa::find($_POST['id_cliente']);
            $cliente->setCredito($_POST['credito']);
            $cliente->store();

            Transaction::close();
        }
    }

    public function show()
    {
        parent::show();
    }
}