<?php

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Traits\DeleteTrait;
use Framework\Traits\EditTrait;
use Framework\Traits\ReloadTrait;
use Framework\Widgets\Container\Panel;
use Framework\Widgets\Container\VBox;
use Framework\Widgets\Datagrid\Datagrid;
use Framework\Widgets\Datagrid\DatagridColumn;
use Framework\Widgets\Datagrid\PageNavigation;
use Framework\Widgets\Form\Combo;
use Framework\Widgets\Form\Entry;
use Framework\Widgets\Form\Form;
use Framework\Widgets\Form\Label;
use Framework\Widgets\Wrapper\DatagridWrapper;
use Framework\Widgets\Wrapper\FormWrapper;

class DespesaFormList extends Page
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
        $this->activeRecord = 'Despesa';
        $this->form = new FormWrapper(new Form('form_busca_despesa'));
        $this->form->setTitle('Pesquisar Despesas');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        
        # Data
        $labelConsultaData = new Label('Data da Despesa');
        $labelConsultaData->setAttribute('class', 'bmd-label-static');
        $consultaData = new Entry('consultadata', 'date');
        $consultaData->setAttribute('class', 'form-control');
        $consultaData->setAttribute('style', 'width: 20%;');

        # Forma
        $labelConsultaForma = new Label('Forma de Pagamento');
        $labelConsultaForma->setAttribute('class', 'bmd-label-static');
        $consultaForma = new Combo('consultaforma');
        $consultaForma->setAttribute('class', 'form-control selectpicker');

        Transaction::open();
        $formas = FormaPagamento::all();
        $itens = [];

        foreach ($formas as $obj_forma) {
            $itens[$obj_forma->getId()] = $obj_forma->getForma();
        }
        $consultaForma->addItens($itens);
        Transaction::close();

        $this->form->addField($labelConsultaData, $consultaData, null, null, 'form-group bmd-form-group');
        $this->form->addField($labelConsultaForma, $consultaForma, null, null, 'form-group bmd-form-group');
        $this->form->addAction('Pesquisar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new DespesaForm, 'onEdit')));

        $this->datagrid = new DatagridWrapper(new Datagrid);
        $this->datagrid->setPanel(new Panel);
        $this->datagrid->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        $codigo = new DatagridColumn('id', 'Código');
        $empresa = new DatagridColumn('empresaNome', 'Empresa');
        $dataDespesa = new DatagridColumn('data_despesa', 'Data da Despesa');
        $valor = new DatagridColumn('valor', 'Valor da Despesa (R$)');
        $forma = new DatagridColumn('formaPagamentoNome', 'Forma de Pagamento');

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($empresa);
        $this->datagrid->addColumn($dataDespesa);
        $this->datagrid->addColumn($valor);
        $this->datagrid->addColumn($forma);

        # Com Material Icons
        //$this->datagrid->addAction('Detalhes', new Action([new DespesaDetails, 'onDetail']), 'id', 'material-icons', 'description');
        $this->datagrid->addAction('Editar', new Action([new DespesaForm, 'onEdit']), 'id', 'material-icons', 'edit');
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

        if ($dados->consultadata) {
            $this->filters[] = ['data_despesa', '=', $dados->consultadata];
        }

        if ($dados->consultaforma) {
            $this->filters[] = ['id_forma_pagamento', '=', $dados->consultaforma];
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