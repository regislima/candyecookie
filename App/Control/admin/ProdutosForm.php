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

class ProdutosForm extends Page
{
    private $activeRecord;
    private $form;
    
    use SaveTrait { onSave as onSaveTrait; }
    use EditTrait;

    public function __construct()
    {
        $this->activeRecord = 'Produto';
        $this->form = new FormWrapper(new Form('form_produtos'));
        $this->form->setTitle('Produtos');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');

        # Código
        $labelCodigo = new Label('Código');
        $labelCodigo->setAttribute('class', 'bmd-label-floating');
        $codigo = new Entry('id');
        $codigo->setAttribute('class', 'form-control');
        $codigo->setAttribute('readonly', null);

        # Nome
        $labelNome = new Label('Nome');
        $labelNome->setAttribute('class', 'bmd-label-floating');
        $nome = new Entry('nome');
        $nome->setAttribute('class', 'form-control');
        $nome->setAttribute('required', null);
        
        # Descrição
        $labelDescricao = new Label('Descrição');
        $labelDescricao->setAttribute('class', 'bmd-label-floating');
        $descricao = new Entry('descricao');
        $descricao->setAttribute('class', 'form-control');
        $descricao->setAttribute('required', null);

        # Estoque
        $labelEstoque = new Label('Estoque');
        $labelEstoque->setAttribute('class', 'bmd-label-floating');
        $estoque = new Entry('estoque');
        $estoque->setAttribute('class', 'form-control');
        $estoque->setAttribute('required', null);
        
        # Preço Custo
        $labelPrecoCusto = new Label('Preço de Custo');
        $labelPrecoCusto->setAttribute('class', 'bmd-label-floating');
        $precoCusto = new Entry('preco_custo');
        $precoCusto->setAttribute('class', 'form-control');
        $precoCusto->setAttribute('required', null);
        
        # Preço Venda
        $labelPrecoVenda = new Label('Preço de Venda');
        $labelPrecoVenda->setAttribute('class', 'bmd-label-floating');
        $precoVenda = new Entry('preco_venda');
        $precoVenda->setAttribute('class', 'form-control');
        $precoVenda->setAttribute('required', null);

        # Medida
        $labelMedida = new Label('Medida');
        $labelMedida->setAttribute('class', 'bmd-label-floating');
        $medida = new Entry('medida');
        $medida->setAttribute('class', 'form-control');
        $medida->setAttribute('required', null);
        
        # Unidade
        $labelUnidade = new Label('Unidade de Medida');
        $labelUnidade->setAttribute('class', 'bmd-label-floating');
        $unidade = new Combo('id_unidade');
        $unidade->setAttribute('class', 'form-control selectpicker');
        $unidade->setAttribute('required', null);

        # Tipo
        $labelTipo = new Label('Tipo');
        $labelTipo->setAttribute('class', 'bmd-label-floating');
        $tipo = new Combo('id_tipo');
        $tipo->setAttribute('class', 'form-control selectpicker');
        $tipo->setAttribute('data-style', 'btn btn-link');
        $tipo->setAttribute('required', null);

        # Fabricante
        $labelFabricante = new Label('Fabricante');
        $labelFabricante->setAttribute('class', 'bmd-label-floating');
        $fabricante = new DataList('id_fabricante');
        $fabricante->setAttribute('class', 'form-control');
        $fabricante->setAttribute('required', null);

        # Add Fabricante
        $addfab = new Element('button');
        $addfab->add('Novo Fabricante');
        $addfab->setAttribute('type', 'button');
        $addfab->setAttribute('class', 'btn btn-sm btn-outline-primary');
        $addfab->setAttribute('data-toggle', 'modal');
        $addfab->setAttribute('data-target', '#modalAddFabricante');
        
        # Imagem
        $labelImagem = new Label('Imagem');
        $labelImagem->setAttribute('class', 'bmd-label-floating');
        $imagem = new Entry('imagem[]', 'file');
        $imagem->setAttribute('multiple', 'multiple');
        $imagem->setAttribute('class', 'form-control-file');

        # Observações
        $labelObs = new Label('Observações');
        $labelObs->setAttribute('class', 'bmd-label-floating');
        $obs = new Text('obs', 5);
        $obs->setAttribute('class', 'form-control');

        Transaction::open();
        $unidades = Unidade::all();
        $itens = [];

        if ($unidades) {
            foreach ($unidades as $obj_unidade) {
                $itens[$obj_unidade->getId()] = $obj_unidade->getSigla();
            }
        }

        $unidade->addItens($itens);
        $tipos = Tipo::all();
        $itens = [];

        if ($tipos) {
            foreach ($tipos as $obj_tipo) {
                $itens[$obj_tipo->getId()] = $obj_tipo->getNome();
            }
        }
        
        $tipo->addItens($itens);
        $fabricantes = Fabricante::all();
        $itens = [];

        if ($fabricantes) {
            foreach ($fabricantes as $obj_fabricante) {
                $itens[$obj_fabricante->getId()] = $obj_fabricante->getNome();
            }
        } else {
            // Faz aparecer o botão de 'Adicionar Fabricante' mesmo sem ter nenhum fabricante cadastrado
            $itens = [0 => ''];
        }
        
        $fabricante->addItens($itens);
        Transaction::close();

        $this->form->addField($labelCodigo, $codigo, null, null, 'form-group');
        $this->form->addField($labelFabricante, $fabricante, $addfab, 'after', 'form-group');
        $this->form->addField($labelNome, $nome, null, null, 'form-group');
        $this->form->addField($labelDescricao, $descricao, null, null, 'form-group');
        $this->form->addField($labelEstoque, $estoque, null, null, 'form-group');
        $this->form->addField($labelPrecoCusto, $precoCusto, null, null, 'form-group');
        $this->form->addField($labelPrecoVenda, $precoVenda, null, null, 'form-group');
        $this->form->addField($labelMedida, $medida, null, null, 'form-group');
        $this->form->addField($labelUnidade, $unidade, null, null, 'form-group');
        $this->form->addField($labelTipo, $tipo, null, null, 'form-group');
        $this->form->addField($labelImagem, $imagem, null, null, null);
        $this->form->addField($labelObs, $obs, null, null, 'form-group');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        
        parent::add($this->form);

        // Modal
        $modal = file_get_contents('App/Resources/Templates/admin/admin_add_fabricante.html');
        parent::add($modal);

        // JavaScript
        $js = new Element('script');
        $js->setAttribute('src', 'App/Resources/assets/js/cadastro_produto.js');
        parent::add($js);
    }

    public function onSave()
    {
        if ($this->onSaveTrait()) {
            header( "Refresh:1; url=?class=ProdutosForm", true, 303);
        }
    }

    public function onAddFabricante()
    {
        if ($_POST) {
            try {
                Transaction::open();

                $fabricante = new Fabricante();
                $fabricante->setNome($_POST['nome_fabricante']);
                $fabricante->setCNPJ($_POST['cnpj_fabricante']);
                $fabricante->setUrl($_POST['url_fabricante']);
                $fabricante->store();

                Transaction::close();
            } catch (Exception $e) {
                Transaction::rollback();
                new Message('error', $e->getMessage());
            }
        }
    }
}