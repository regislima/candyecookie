<?php

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Traits\DeleteTrait;
use Framework\Traits\EditTrait;
use Framework\Traits\ReloadTrait;
use Framework\Widgets\Base\Element;
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

class ContatoFormList extends Page
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
        $this->activeRecord = 'Contato';
        $this->form = new FormWrapper(new Form('form_busca_contatos'));
        $this->form->setTitle('Pesquisar Contatos');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        
        # Nome
        $labelNome = new Label('CPF');
        $labelNome->setAttribute('class', 'bmd-label-floating');
        $nome = new Entry('nome');
        $nome->setAttribute('class', 'form-control');

        # Lida
        $labelLida = new Label('Lida');
        $labelLida->setAttribute('class', 'form-check-label');
        $lida = new Entry('lida', 'checkbox');
        $lida->setAttribute('class', 'form-check-input');
        $span = new Element('span');
        $span->setAttribute('class', 'form-check-sign');
        $subSpan = new Element('span');
        $subSpan->setAttribute('class', 'check');
        $span->add($subSpan);
        $labelLida->add($lida);
        $labelLida->add($span);
        
        $this->form->addField($labelNome, $nome, null, null, 'form-group bmd-form-group');
        $this->form->addField($labelLida, $lida, null, null, 'form-check');
        $this->form->addAction('Pesquisar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new ContatoForm, 'onReload')));

        $this->datagrid = new DatagridWrapper(new Datagrid);
        $this->datagrid->setPanel(new Panel);
        $this->datagrid->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        
        $codigo = new DatagridColumn('id', 'Código');
        $cliente = new DatagridColumn('id_cliente', 'Cliente');
        $assunto = new DatagridColumn('assunto', 'Assunto');
        $dataHora = new DatagridColumn('data_hora', 'Data e Hora');
        $lida = new DatagridColumn('lida', 'Lida');

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($cliente);
        $this->datagrid->addColumn($assunto);
        $this->datagrid->addColumn($dataHora);
        $this->datagrid->addColumn($lida);

        # Com Material Icons
        $this->datagrid->addAction('Responder', new Action([new ContatoForm, 'onReload']), 'id', 'material-icons', 'reply');
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
            Transaction::open();

            $criteria = new Criteria;
            $criteria->add('cpf', '=', $dados->nome);

            $repo = new Repository('Pessoa');
            $pessoas = $repo->load($criteria);

            Transaction::close();

            if ($pessoas) {
                $this->filters[] = ['id_cliente', '=', $pessoas[0]->getId()];
            }
        }

        $this->filters[] = ['lida', '=', $dados->lida ? 'S' : 'N'];

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