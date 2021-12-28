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
use Framework\Widgets\Form\Entry;
use Framework\Widgets\Form\Form;
use Framework\Widgets\Form\Label;
use Framework\Widgets\Wrapper\DatagridWrapper;
use Framework\Widgets\Wrapper\FormWrapper;

class ProdutosFormList extends Page
{
    private $activeRecord;
    private $form;
    private $datagrid;
    private $loaded;
    private $filters;
    private $pagNav;
    
    use EditTrait;
    use DeleteTrait { ondelete as onDeleteTrait; }
    use ReloadTrait { onReload as onReloadTrait; }

    public function __construct()
    {
        $this->activeRecord = 'Produto';
        $this->form = new FormWrapper(new Form('form_busca_produto'));
        $this->form->setTitle('Pesquisar Produto');
        $this->form->setPanel(new Panel);
        $this->form->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        
        # Nome
        $labelDesc = new Label('Nome');
        $labelDesc->setAttribute('class', 'bmd-label-floating');
        $nomeOuCPF = new Entry('nome');
        $nomeOuCPF->setAttribute('class', 'form-control');

        $this->form->addField($labelDesc, $nomeOuCPF, null, null, 'form-group bmd-form-group');
        $this->form->addAction('Pesquisar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new ProdutosForm, 'onEdit')));

        $this->datagrid = new DatagridWrapper(new Datagrid);
        $this->datagrid->setPanel(new Panel);
        $this->datagrid->getPanel()->getHead()->setAttribute('class', 'card-header card-header-primary');
        $codigo = new DatagridColumn('id', 'Código');
        $nomeProduto = new DatagridColumn('nome', 'Nome');
        $estoque = new DatagridColumn('estoque', 'Estoque');
        $precoCusto = new DatagridColumn('preco_custo', 'Preço de Custo (R$)');
        $precoVenda = new DatagridColumn('preco_venda', 'Preço de Venda (R$)');
        $margemLucro = new DatagridColumn('margem_lucro', "Margem de Lucro (%)");

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($nomeProduto);
        $this->datagrid->addColumn($estoque);
        $this->datagrid->addColumn($precoCusto);
        $this->datagrid->addColumn($precoVenda);
        $this->datagrid->addColumn($margemLucro);

        # Com FontAwsome
        #$this->datagrid->addAction('Editar', new Action([new PessoasForm, 'onEdit']), 'id', 'fa fa-edit');
        #$this->datagrid->addAction('Excluir', new Action([$this, 'onDelete']), 'id', 'fa fa-rash');

        # Com Material Icons
        $this->datagrid->addAction('Detalhes', new Action([new ProdutoDetails, 'onDetail']), 'id', 'material-icons', 'description');
        $this->datagrid->addAction('Editar', new Action([new ProdutosForm, 'onEdit']), 'id', 'material-icons', 'edit');
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
            $this->filters[] = ['nome', 'like', "%{$dados->nome}%", 'and'];
        }

        $this->onReloadTrait();
        $this->loaded = true;
    }

    public function onDelete()
    {
        Transaction::open();
        $produto = Produto::find($_GET['id']);
        Transaction::close();

        $this->onDeleteTrait($_GET);
        
        // Remove as imagens do produto
        if ($produto) {
            Transaction::open();

            foreach ($produto->getImagem() as $value) {
                $value->delete();
                ImagemHelper::remove($value->getImagem());
            }

            Transaction::close();
        }
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }

        parent::show();
    }
}