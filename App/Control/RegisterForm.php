<?php

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Session\Session;
use Framework\Traits\EditTrait;
use Framework\Traits\SaveTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Container\Panel;
use Framework\Widgets\Dialog\Message;
use Framework\Widgets\Form\DataList;
use Framework\Widgets\Form\Entry;
use Framework\Widgets\Form\Form;
use Framework\Widgets\Form\Group;
use Framework\Widgets\Form\Label;
use Framework\Widgets\Wrapper\FormWrapper;

class RegisterForm extends Page
{
    private $activeRecord;
    private $form;
    
    use SaveTrait { onSave as onSaveTrait; }
    use EditTrait;

    public function __construct()
    {
        if (isset($_GET['redirect'])) {
            Session::setValue('redirect', $_GET['redirect']);
        }

        $this->activeRecord = 'Pessoa';
        $this->form = new FormWrapper(new Form('form_pessoas'));
        $this->form->setTitle('Perfil');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->setAttribute('class', 'mt-5');
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');

        # Código
        $labelCodigo = new Label('Código');
        $codigo = new Entry('id');
        $codigo->setAttribute('class', 'form-control');
        $codigo->setAttribute('id', 'id_pessoa');
        $codigo->setAttribute('readonly', null);
        
        # Nome
        $labelNome = new Label('Nome');
        $nome = new Entry('nome');
        $nome->setAttribute('class', 'form-control');
        $nome->setAttribute('id', 'nome_pessoa');
        $nome->setAttribute('required', null);
        $nome->setAttribute('autofocus', null);

        # CPF
        $labelCPF = new Label('CPF');
        $cpf = new Entry('cpf');
        $cpf->setAttribute('class', 'form-control');
        $cpf->setAttribute('id', 'cpf_pessoa');
        $cpf->setAttribute('required', null);

        # Endereço
        $labelEndereco = new Label('Endereço');
        $endereco = new Entry('endereco');
        $endereco->setAttribute('class', 'form-control');
        $endereco->setAttribute('id', 'endereco_pessoa');
        $endereco->setAttribute('required', null);
        
        # Bairro
        $labelBairro = new Label('Bairro');
        $bairro = new Entry('bairro');
        $bairro->setAttribute('class', 'form-control');
        $bairro->setAttribute('id', 'bairro_pessoa');
        $bairro->setAttribute('required', null);
        
        # Telefone
        $labelTelefone = new Label('Telefone');
        $telefone = new Entry('telefone', 'tel');
        $telefone->setAttribute('class', 'form-control');
        $telefone->setAttribute('id', 'telefone_pessoa');
        
        # Email
        $labelEmail = new Label('Email');
        $email = new Entry('email', 'email');
        $email->setAttribute('class', 'form-control');
        $email->setAttribute('id', 'email_pessoa');
        
        # Senha
        if ((isset($_GET['method']) and $_GET['method'] != 'onEdit') or !isset($_GET['method'])) {
            $labelSenha = new Label('Senha');
            $senha = new Entry('senha', 'password');
            $senha->setAttribute('class', 'form-control');
            $senha->setAttribute('id', 'senha_pessoa');
            $senha->setAttribute('required', null);
        }

        # Nascimento
        $labelNascimento = new Label('Data de Nascimento');
        $nascimento = new Entry('nascimento');
        $nascimento->setAttribute('class', 'form-control');
        $nascimento->setAttribute('id', 'nascimento_pessoa');
        $nascimento->setAttribute('required', null);
        
        # Cidade
        $labelCidade = new Label('Cidade');
        $cidade = new DataList('id_cidade');
        $cidade->setAttribute('class', 'form-control');
        $cidade->setAttribute('required', null);

        $labelGrupo = new Label('Grupo');
        $grupo = new Group('id_grupo', 'radio');
        $grupo->setAttribute('id', 'grupo_pessoa');
        $grupo->addItens([2 => 'Cliente']);
        
        # Imagem
        $labelImagem = new Label('Imagem');
        $imagem = new Entry('imagem', 'file');
        $imagem->setAttribute('class', 'form-control-file');
        $imagem->setAttribute('id', 'imagem_pessoa');

        Transaction::open();

        $cidades = Cidade::all();
        $itens = [];

        foreach ($cidades as $obj_cidade) {
            $itens[$obj_cidade->getId()] = $obj_cidade->getNome();
        }

        $cidade->addItens($itens);
        Transaction::close();

        $this->form->addField($labelCodigo, $codigo, null, null, 'form-group');
        $this->form->addField($labelNome, $nome, null, null, 'form-group');
        $this->form->addField($labelCPF, $cpf, null, null, 'form-group');
        $this->form->addField($labelEndereco, $endereco, null, null, 'form-group');
        $this->form->addField($labelBairro, $bairro, null, null, 'form-group');
        $this->form->addField($labelTelefone, $telefone, null, null, 'form-group');
        $this->form->addField($labelEmail, $email, null, null, 'form-group');

        if ((isset($_GET['method']) and $_GET['method'] != 'onEdit') or !isset($_GET['method'])) {
            $this->form->addField($labelSenha, $senha, null, null, 'form-group');
        }
        
        $this->form->addField($labelNascimento, $nascimento, null, null, 'form-group');
        $this->form->addField($labelCidade, $cidade, null, 'after', 'form-group');
        $this->form->addField($labelGrupo, $grupo, null, null, 'form-group');
        $this->form->addField($labelImagem, $imagem, null, null, null);
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        parent::add($this->form);

        // JavaScript
        $js = new Element('script');
        $script = file_get_contents('App/Resources/assets/js/cadastro_pessoa.js');
        $js->add($script);
        parent::add($js);
    }

    public function onSave()
    {
        try {
            Transaction::open();

            $class = $this->activeRecord;
            $dados = $this->form->getData($class);

            if (is_object($dados)) {
                $dados->store();
                new Message('info', 'Dados armazenados com sucesso');
                Session::setValue('logged', $dados);
                Transaction::close();
                
                if (Session::getValue('redirect')) {
                    header("Location: index.php?class=" . Session::getValue('redirect'));
                } else {
                    header("Location: index.php");
                }
            } else {
                new Message('info', $dados);
            }

        } catch (Exception $e) {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
    }
}