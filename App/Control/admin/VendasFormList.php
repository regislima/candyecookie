<?php

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Traits\DeleteTrait;
use Framework\Traits\EditTrait;
use Framework\Traits\ReloadTrait;
use Framework\Widgets\Container\Panel;
use Framework\Widgets\Container\VBox;
use Framework\Widgets\Datagrid\Datagrid;
use Framework\Widgets\Datagrid\DatagridColumn;
use Framework\Widgets\Datagrid\PageNavigation;
use Framework\Widgets\Form\Entry;
use Framework\Widgets\Form\Form;
use Framework\Widgets\Form\Label;
use Framework\Widgets\Wrapper\DatagridWrapper;
use Framework\Widgets\Wrapper\FormWrapper;

class VendasFormList extends Page
{
    private $activeRecord;
    private $form;
    private $datagrid;
    private $loaded;
    private $filters;
    private $pagNav;
    
    use EditTrait;
    use DeleteTrait;
    use ReloadTrait { onReload as onReloadTrait; }

    public function __construct()
    {
        $this->activeRecord = 'Venda';
        $this->form = new FormWrapper(new Form('form_busca_venda'));
        $this->form->setTitle('Pesquisar Vendas');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        
        # Código
        $labelCod = new Label('Código');
        $labelCod->setAttribute('class', 'bmd-label-floating');
        $codigoVenda = new Entry('codigo');
        $codigoVenda->setAttribute('class', 'form-control');

        $this->form->addField($labelCod, $codigoVenda, null, null, 'form-group bmd-form-group');
        $this->form->addAction('Pesquisar', new Action(array($this, 'onReload')));

        $this->datagrid = new DatagridWrapper(new Datagrid);
        $this->datagrid->setPanel(new Panel);
        $this->datagrid->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        $codigo = new DatagridColumn('id', 'Código');
        $clienteVenda = new DatagridColumn('id_cliente', 'Cliente');
        $dataVenda = new DatagridColumn('data_venda', 'Data');
        $valorVenda = new DatagridColumn('subtotal', 'Valor (R$)');
        $descontoVenda = new DatagridColumn('desconto', 'Descontos');
        $acrescimosVenda = new DatagridColumn('acrescimos', 'Frete + Taxas');
        $valorFinalVenda = new DatagridColumn('valor_final', "Valor Final (R$)");

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($clienteVenda);
        $this->datagrid->addColumn($dataVenda);
        $this->datagrid->addColumn($valorVenda);
        $this->datagrid->addColumn($descontoVenda);
        $this->datagrid->addColumn($acrescimosVenda);
        $this->datagrid->addColumn($valorFinalVenda);

        # Com FontAwsome
        #$this->datagrid->addAction('Editar', new Action([new PessoasForm, 'onEdit']), 'id', 'fa fa-edit');
        #$this->datagrid->addAction('Excluir', new Action([$this, 'onDelete']), 'id', 'fa fa-rash');

        # Com Material Icons
        $this->datagrid->addAction('Detalhes', new Action([new VendaDetails, 'onDetail']), 'id', 'material-icons', 'description');
        $this->datagrid->addAction('Excluir', new Action([$this, 'onDelete']), 'id', 'material-icons text-danger', 'close');

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
        }

        $this->onReloadTrait();
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