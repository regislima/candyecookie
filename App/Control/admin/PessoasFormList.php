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

class PessoasFormList extends Page
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
        $this->activeRecord = 'Pessoa';
        $this->form = new FormWrapper(new Form('form_busca_pessoas'));
        $this->form->setTitle('Pesquisar Cliente');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        
        # Nome
        $labelNome = new Label('Nome ou CPF');
        $labelNome->setAttribute('class', 'bmd-label-floating');
        $nome = new Entry('nome');
        $nome->setAttribute('class', 'form-control');

        $this->form->addField($labelNome, $nome, null, null, 'form-group bmd-form-group');
        $this->form->addAction('Pesquisar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new PessoasForm, 'onEdit')));

        $this->datagrid = new DatagridWrapper(new Datagrid);
        $this->datagrid->setPanel(new Panel);
        $this->datagrid->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        
        $codigo = new DatagridColumn('id', 'Código');
        $nome = new DatagridColumn('nome', 'Nome');
        $endereco = new DatagridColumn('endereco', 'Endereço');
        $telefone = new DatagridColumn('telefone', 'Telefone');
        $email = new DatagridColumn('email', 'Email');
        $nascimento = new DatagridColumn('nascimento', 'Data de Nascimento');

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($endereco);
        $this->datagrid->addColumn($telefone);
        $this->datagrid->addColumn($email);
        $this->datagrid->addColumn($nascimento);

        # Com FontAwsome
        #$this->datagrid->addAction('Editar', new Action([new PessoasForm, 'onEdit']), 'id', 'fa fa-edit');
        #$this->datagrid->addAction('Excluir', new Action([$this, 'onDelete']), 'id', 'fa fa-trash');

        # Com Material Icons
        $this->datagrid->addAction('Detalhes', new Action([new PessoaDetails, 'onDetail']), 'id', 'material-icons', 'description');
        $this->datagrid->addAction('Editar', new Action([new PessoasForm, 'onEdit']), 'id', 'material-icons', 'edit');
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

        if ($dados->nome) {
            $this->filters[] = ['nome', 'like', "%{$dados->nome}%"];
            $this->filters[] = ['cpf', '=', "{$dados->nome}", 'or'];
        }

        $this->filters[] = ['id_grupo', '!=', 1];

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