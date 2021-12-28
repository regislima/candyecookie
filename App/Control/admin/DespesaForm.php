<?php

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Traits\EditTrait;
use Framework\Traits\SaveTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Container\Panel;
use Framework\Widgets\Dialog\Message;
use Framework\Widgets\Form\Combo;
use Framework\Widgets\Form\DataList;
use Framework\Widgets\Form\Entry;
use Framework\Widgets\Form\Form;
use Framework\Widgets\Form\Label;
use Framework\Widgets\Form\Text;
use Framework\Widgets\Wrapper\FormWrapper;

class DespesaForm extends Page
{
    private $activeRecord;
    private $form;
    
    use SaveTrait;
    use EditTrait;

    public function __construct()
    {
        $this->activeRecord = 'Despesa';
        $this->form = new FormWrapper(new Form('form_despesas'));
        $this->form->setTitle('Despesas');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');

        # Código
        $labelCodigo = new Label('Código');
        $labelCodigo->setAttribute('class', 'bmd-label-floating');
        $codigo = new Entry('id');
        $codigo->setAttribute('class', 'form-control');
        $codigo->setAttribute('readonly', null);

        # Empresa
        $labelEmpresa = new Label('Empresa');
        $labelEmpresa->setAttribute('class', 'bmd-label-floating');
        $empresa = new DataList('id_empresa');
        $empresa->setAttribute('class', 'form-control');
        $empresa->setAttribute('required', null);

        # Add Empresa
        $addEmp = new Element('button');
        $addEmp->add('Nova Empresa');
        $addEmp->setAttribute('type', 'button');
        $addEmp->setAttribute('class', 'btn btn-sm btn-outline-primary');
        $addEmp->setAttribute('data-toggle', 'modal');
        $addEmp->setAttribute('data-target', '#modalAddFabricante');

        # Data
        $labelDataDespesa = new Label('Data da Despesa');
        $labelDataDespesa->setAttribute('class', 'bmd-label-static');
        $dataDespesa = new Entry('data_despesa', 'date');
        $dataDespesa->setAttribute('class', 'form-control');
        $dataDespesa->setAttribute('required', null);

        # Valor
        $labelValor = new Label('Valor da Despesa');
        $labelValor->setAttribute('class', 'bmd-label-floating');
        $valor = new Entry('valor');
        $valor->setAttribute('class', 'form-control');
        $valor->setAttribute('required', null);

        # Forma de Pagamento
        $labelFormaPagamento = new Label('Forma de Pagamento');
        $labelFormaPagamento->setAttribute('class', 'bmd-label-floating');
        $formaPagamento = new Combo('id_forma_pagamento');
        $formaPagamento->setAttribute('class', 'form-control selectpicker');
        $formaPagamento->setAttribute('required', null);
        
        # Descrição
        $labelDescricao = new Label('Descrição da Despesa');
        $labelDescricao->setAttribute('class', 'bmd-label-floating');
        $descricao = new Text('descricao');
        $descricao->setAttribute('class', 'form-control');
        $descricao->setAttribute('required', null);
        
        # Dias Cheque
        $labelDiasCheque = new Label('Dias para resgatar cheque');
        $labelDiasCheque->setAttribute('class', 'bmd-label-floating');
        $diasCheque = new Entry('dias_cheque');
        $diasCheque->setAttribute('class', 'form-control');

        Transaction::open();

        $empresas = Fabricante::all();
        $itens = [];

        if ($empresas) {
            foreach ($empresas as $obj_empresa) {
                $itens[$obj_empresa->getId()] = $obj_empresa->getNome();
            }
        } else {
            // Faz aparecer o botão de 'Adicionar Fabricante' mesmo sem ter nenhum fabricante cadastrado
            $itens = [0 => ''];
        }
        
        $empresa->addItens($itens);

        $formas = FormaPagamento::all();
        $itens = [];

        if ($formas) {
            foreach ($formas as $obj_forma) {
                $itens[$obj_forma->getId()] = $obj_forma->getForma();
            }
        }
        
        $formaPagamento->addItens($itens);

        Transaction::close();

        $this->form->addField($labelCodigo, $codigo, null, null, 'form-group');
        $this->form->addField($labelEmpresa, $empresa, $addEmp, 'after', 'form-group');
        $this->form->addField($labelDataDespesa, $dataDespesa, null, null, 'form-group');
        $this->form->addField($labelValor, $valor, null, null, 'form-group');
        $this->form->addField($labelFormaPagamento, $formaPagamento, null, null, 'form-group');
        $this->form->addField($labelDescricao, $descricao, null, null, 'form-group');
        $this->form->addField($labelDiasCheque, $diasCheque, null, null, 'form-group');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        
        parent::add($this->form);

        // Modal
        $modal = file_get_contents('App/Resources/Templates/admin/admin_add_fabricante.html');
        parent::add($modal);

        // JavaScript
        $js = new Element('script');
        $js->setAttribute('src', 'App/Resources/assets/js/cadastro_despesa.js');
        parent::add($js);
    }

    public function onAddFabricante()
    {
        if ($_POST) {
            try {
                Transaction::open();

                $empresa = new Fabricante();
                $empresa->setNome($_POST['nome_fabricante']);
                $empresa->setCNPJ($_POST['cnpj_fabricante']);
                $empresa->setUrl($_POST['url_fabricante']);
                $empresa->store();

                Transaction::close();
            } catch (Exception $e) {
                Transaction::rollback();
                new Message('error', $e->getMessage());
            }
        }
    }
}