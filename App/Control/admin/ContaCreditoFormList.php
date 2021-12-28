<?php

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Traits\DeleteTrait;
use Framework\Widgets\Container\Panel;
use Framework\Widgets\Container\VBox;
use Framework\Widgets\Datagrid\Datagrid;
use Framework\Widgets\Datagrid\DatagridColumn;
use Framework\Widgets\Datagrid\PageNavigation;
use Framework\Widgets\Dialog\Message;
use Framework\Widgets\Form\Entry;
use Framework\Widgets\Form\Form;
use Framework\Widgets\Form\Label;
use Framework\Widgets\Wrapper\DatagridWrapper;
use Framework\Widgets\Wrapper\FormWrapper;

class ContaCreditoFormList extends Page
{
    private $activeRecord;
    private $form;
    private $datagrid;
    private $loaded;
    private $filters;
    private $pagNav;
    
    use DeleteTrait;

    public function __construct()
    {
        $this->activeRecord = 'Conta';
        $this->form = new FormWrapper(new Form('form_busca_conta'));
        $this->form->setTitle('Pesquisar Conta');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        
        # Código
        $labelCod = new Label('Códido do Cliente ou Código da Conta');
        $labelCod->setAttribute('class', 'bmd-label-floating');
        $codigoConta = new Entry('codigo');
        $codigoConta->setAttribute('class', 'form-control');

        $this->form->addField($labelCod, $codigoConta, null, null, 'form-group bmd-form-group');
        $this->form->addAction('Pesquisar', new Action(array($this, 'onReload')));

        $this->datagrid = new DatagridWrapper(new Datagrid);
        $this->datagrid->setPanel(new Panel);
        $this->datagrid->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        $codigoCliente = new DatagridColumn('id_cliente', 'Cliente');
        $nomeCliente = new DatagridColumn('clienteNome', 'Nome');

        $this->datagrid->addColumn($codigoCliente);
        $this->datagrid->addColumn($nomeCliente);

        # Com Material Icons
        $this->datagrid->addAction('Detalhes', new Action([new ContaCreditoDetails, 'onReload']), 'id_cliente', 'material-icons', 'description');

        $this->pagNav = new PageNavigation;
        $this->pagNav->setAction(new Action([$this, 'onReload']));

        $box = new VBox;
        $box->add($this->form);
        $box->add($this->datagrid);
        $box->add($this->pagNav);

        parent::add($box);
    }

    public function onReload()
    {
        $dados = $this->form->getDataSeachForm();

        if ($dados->codigo) {
            $this->filters[] = ['id', '=', $dados->codigo];
            $this->filters[] = ['id_cliente', '=', $dados->codigo, 'or'];
        }
        
        try {
            Transaction::open();
            $repository = new Repository($this->activeRecord);
            $criteria = new Criteria;

            if (isset($this->filters)) {
                foreach ($this->filters as $filter) {
                    if (isset($filter[3])) {
                        $criteria->add($filter[0], $filter[1], $filter[2], $filter[3]);
                    } else {
                        $criteria->add($filter[0], $filter[1], $filter[2]);
                    }
                }
            }

            // Conta o numero de registros que satisfazem o critério
            $count = $repository->count($criteria);
            
            $criteria->setProperty('order', 'id');
            $criteria->setProperty('limit', '10');
            $criteria->setProperty('group', 'id_cliente');
            $criteria->setProperty('offset', isset($_GET['offset']) ? (int) $_GET['offset'] : 0);

            $objects = $repository->load($criteria);
            $this->datagrid->clear();

            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $this->pagNav->setTotalRecords($count);
            $this->pagNav->setCurrentPage(isset($_GET['page']) ? (int) $_GET['page'] : 1);

            Transaction::close();
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
        
        $this->loaded = true;
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }

        parent::show();
    }
}