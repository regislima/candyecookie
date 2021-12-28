<?php

// Em Desenvolcimento

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Traits\EditTrait;
use Framework\Traits\SaveTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Container\Panel;
use Framework\Widgets\Form\DataList;
use Framework\Widgets\Form\Entry;
use Framework\Widgets\Form\Form;
use Framework\Widgets\Form\Group;
use Framework\Widgets\Form\Label;
use Framework\Widgets\Wrapper\FormWrapper;

class VendaForm extends Page
{
    private $activeRecord;
    private $form;
    
    use SaveTrait;
    use EditTrait;

    public function __construct()
    {
        $this->activeRecord = 'Pessoa';
        $this->form = new FormWrapper(new Form('form_venda'));
        $this->form->setTitle('Vendas');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');

        # Código
        $labelCodigo = new Label('Código');
        $labelCodigo->setAttribute('class', 'bmd-label-floating');
        $codigo = new Entry('id');
        $codigo->setAttribute('class', 'form-control');
        $codigo->setAttribute('id', 'id_venda');
        $codigo->setAttribute('readonly', null);
        
        # Cliente
        $labelCliente = new Label('Cliente');
        $labelCliente->setAttribute('class', 'bmd-label-floating');
        $cliente = new DataList('id_cliente');
        $cliente->setAttribute('class', 'form-control');
        $cliente->setAttribute('required', null);
        
        # Grupo
        # Estilo para cada item da forma de pagamento
        $styleItem = [
            'div' => 'form-check form-check-radio',
            'item' => 'form-check-input',
            'label' => 'form-check-label',
            'span' => "<span class='circle'><span class='check'></span></span>"
        ];

        $labelFormaPagamento = new Label('Forma de Pagamento');
        $labelFormaPagamento->setAttribute('class', 'bmd-label-floating');
        $formaPagamento = new Group('id_forma_pagamento', 'radio');
        $formaPagamento->AddStyle($styleItem);
        $formaPagamento->setAttribute('id', 'grupo_forma_pagamento');

        # Data da Venda
        $labelDataVenda = new Label('Data da Venda');
        $labelDataVenda->setAttribute('class', 'bmd-label-floating');
        $dataVenda = new Entry('data_venda', 'date');
        $dataVenda->setAttribute('class', 'form-control');
        $dataVenda->setAttribute('id', 'data_venda');
        $dataVenda->setAttribute('required', null);

        Transaction::open();

        $clientes = Pessoa::all();
        $itens = [];

        foreach ($clientes as $obj_cliente) {
            $itens[$obj_cliente->getId()] = $obj_cliente->getNome();
        }

        $cliente->addItens($itens);
        $formasPagamento = FormaPagamento::all();
        $itens = [];

        foreach ($formasPagamento as $obj_formaPagamento) {
            $itens[$obj_formaPagamento->getId()] = $obj_formaPagamento->getForma();
        }

        $formaPagamento->addItens($itens);

        Transaction::close();

        $this->form->addField($labelCodigo, $codigo, null, null, 'form-group');
        $this->form->addField($labelCliente, $cliente, null, null, 'form-group');
        $this->form->addField($labelFormaPagamento, $formaPagamento, null, null, 'form-group');
        $this->form->addField($labelDataVenda, $dataVenda, null, null, 'form-group');
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        parent::add($this->form);

        // JavaScript
        $js = new Element('script');
        $js->setAttribute('src', 'App/Resources/assets/js/cadastro_pessoa.js');
        parent::add($js);
    }
}