<?php

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Session\Session;
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

class PessoasForm extends Page
{
    private $activeRecord;
    private $form;
    
    use SaveTrait;
    use EditTrait;

    public function __construct()
    {
        $this->activeRecord = 'Pessoa';
        $this->form = new FormWrapper(new Form('form_pessoas'));
        $this->form->setTitle('Perfil');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');

        # Código
        $labelCodigo = new Label('Código');
        $labelCodigo->setAttribute('class', 'bmd-label-floating');
        $codigo = new Entry('id');
        $codigo->setAttribute('class', 'form-control');
        $codigo->setAttribute('id', 'id_pessoa');
        $codigo->setAttribute('readonly', null);
        
        # Nome
        $labelNome = new Label('Nome');
        $labelNome->setAttribute('class', 'bmd-label-floating');
        $nome = new Entry('nome');
        $nome->setAttribute('class', 'form-control');
        $nome->setAttribute('id', 'nome_pessoa');
        $nome->setAttribute('required', null);
        $nome->setAttribute('autofocus', null);

        # CPF
        $labelCPF = new Label('CPF');
        $labelCPF->setAttribute('class', 'bmd-label-floating');
        $cpf = new Entry('cpf');
        $cpf->setAttribute('class', 'form-control');
        $cpf->setAttribute('id', 'cpf_pessoa');
        $cpf->setAttribute('required', null);

        # Endereço
        $labelEndereco = new Label('Endereço');
        $labelEndereco->setAttribute('class', 'bmd-label-floating');
        $endereco = new Entry('endereco');
        $endereco->setAttribute('class', 'form-control');
        $endereco->setAttribute('id', 'endereco_pessoa');
        $endereco->setAttribute('required', null);
        
        # Bairro
        $labelBairro = new Label('Bairro');
        $labelBairro->setAttribute('class', 'bmd-label-floating');
        $bairro = new Entry('bairro');
        $bairro->setAttribute('class', 'form-control');
        $bairro->setAttribute('id', 'bairro_pessoa');
        $bairro->setAttribute('required', null);
        
        # Telefone
        $labelTelefone = new Label('Telefone');
        $labelTelefone->setAttribute('class', 'bmd-label-floating');
        $telefone = new Entry('telefone', 'tel');
        $telefone->setAttribute('class', 'form-control');
        $telefone->setAttribute('id', 'telefone_pessoa');
        
        # Email
        $labelEmail = new Label('Email');
        $labelEmail->setAttribute('class', 'bmd-label-floating');
        $email = new Entry('email', 'email');
        $email->setAttribute('class', 'form-control');
        $email->setAttribute('id', 'email_pessoa');

        # Nascimento
        $labelNascimento = new Label('Data de Nascimento');
        $labelNascimento->setAttribute('class', 'bmd-label-floating');
        $nascimento = new Entry('nascimento');
        $nascimento->setAttribute('class', 'form-control');
        $nascimento->setAttribute('id', 'nascimento_pessoa');
        $nascimento->setAttribute('required', null);
        
        # Cidade
        $labelCidade = new Label('Cidade');
        $labelCidade->setAttribute('class', 'bmd-label-floating');
        $cidade = new DataList('id_cidade');
        $cidade->setAttribute('class', 'form-control');
        $cidade->setAttribute('required', null);
        
        # Grupo
        # Estilo para cada item do grupo
        $styleItem = [
            'div' => 'form-check form-check-radio',
            'item' => 'form-check-input',
            'label' => 'form-check-label',
            'span' => "<span class='circle'><span class='check'></span></span>"
        ];

        $labelGrupo = new Label('Grupo');
        $labelGrupo->setAttribute('class', 'bmd-label-floating');
        $grupo = new Group('id_grupo', 'radio');
        $grupo->AddStyle($styleItem);
        $grupo->setAttribute('id', 'grupo_pessoa');
        
        # Imagem
        $labelImagem = new Label('Imagem');
        $labelImagem->setAttribute('class', 'bmd-label-floating');
        $imagem = new Entry('imagem', 'file');
        $imagem->setAttribute('class', 'form-control-file');
        $imagem->setAttribute('id', 'imagem_pessoa');

        # Crédito
        $labelCredito = new Label('Crédito');
        $labelCredito->setAttribute('class', 'bmd-label-floating');
        $credito = new Entry('credito');
        $credito->setAttribute('class', 'form-control');

        Transaction::open();

        $cidades = Cidade::all();
        $itens = [];

        foreach ($cidades as $obj_cidade) {
            $itens[$obj_cidade->getId()] = $obj_cidade->getNome();
        }

        $cidade->addItens($itens);
        $grupos = Grupo::all();
        $itens = [];

        foreach ($grupos as $obj_grupo) {
            $itens[$obj_grupo->getId()] = $obj_grupo->getNome();
        }

        $grupo->addItens($itens);
        Transaction::close();

        $this->form->addField($labelCodigo, $codigo, null, null, 'form-group');
        $this->form->addField($labelNome, $nome, null, null, 'form-group');
        $this->form->addField($labelCPF, $cpf, null, null, 'form-group');
        $this->form->addField($labelEndereco, $endereco, null, null, 'form-group');
        $this->form->addField($labelBairro, $bairro, null, null, 'form-group');
        $this->form->addField($labelTelefone, $telefone, null, null, 'form-group');
        $this->form->addField($labelEmail, $email, null, null, 'form-group');
        $this->form->addField($labelNascimento, $nascimento, null, null, 'form-group');
        $this->form->addField($labelCidade, $cidade, null, null, 'form-group');
        $this->form->addField($labelGrupo, $grupo, null, null, 'form-group');
        
        if (Session::getValue('logged')->getGrupo() == 'Administrador') {
            $this->form->addField($labelCredito, $credito, null, null, 'form-group');
        }

        $this->form->addField($labelImagem, $imagem, null, null, null);
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        parent::add($this->form);

        // JavaScript
        $js = new Element('script');
        $js->setAttribute('src', 'App/Resources/assets/js/cadastro_pessoa.js');
        parent::add($js);
    }
}