<?php

use Framework\Control\Action;
use Framework\Control\Page;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Session\Session;
use Framework\Widgets\Datagrid\PageNavigation;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class LojaIndex extends Page
{
    private $activeRecord;
    private $template;
    private $pagNav;
    private $replaces;
    private $loja;

    public function __construct()
    {
        $this->activeRecord = 'Produto';
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $this->template = $twig->load('loja_index.html');

        $this->pagNav = new PageNavigation;
        $this->pagNav->setAction(new Action([$this, 'onReload']));
    }

    public function onReload()
    {
        $this->replaces['slider'] = self::getArquivosSlider();

        try {
            Transaction::open();
            $repository = new Repository($this->activeRecord);
            $criteria = new Criteria;

            if ($_POST) {
                $criteria->add('nome', 'like', "%{$_POST['pesquisar']}%");
            }

            // Conta o numero de registros que satisfazem ao critério
            $count = $repository->count($criteria);
            
            $criteria->setProperty('order', 'id');
            $criteria->setProperty('limit', '10');
            $criteria->setProperty('offset', isset($_GET['offset']) ? (int) $_GET['offset'] : 0);

            $this->replaces['produtos'] = $repository->load($criteria);
            $this->replaces['pagination'] = $this->pagNav;

            $this->pagNav->setTotalRecords($count);
            $this->pagNav->setCurrentPage(isset($_GET['page']) ? (int) $_GET['page'] : 1);

            $this->loja = $this->template->render($this->replaces);

            Transaction::close();
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }

    public function onAddCart()
    {
        try {
            Transaction::open();
            $p = Produto::find($_GET['id']);

            if ($p) {
                $produto = (object) $p->toArray();
                $produto->quantidade = 1;

                if ($p->getImagem()) {
                    $produto->imagem = $p->getImagem()[0]->getImagem();
                }
                
                // Recupera todos os itens do carrinho
                $list = Session::getValue('carrinho');
                
                // Verifica se o item ainda não foi adicionado
                if (!isset($list[$produto->id])) {
                    $list[$produto->id] = $produto;
                    Session::setValue('carrinho', $list);
                }
            }

            Transaction::close();
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }

    public function show()
    {
        $this->onReload();
        parent::add($this->loja);
        parent::show();
    }

    /**
     * Método que recupera os arquivos que serão exibidos no slider.
     * 
     * @return array Array de arquivos.
     */
    static public function getArquivosSlider()
    {
        $i = 1;
        $array = [];
        $dir = new DirectoryIterator('App/Resources/Images/Slider');

        foreach ($dir as $value) {
            if ($value->getFilename() != '.' and $value->getFilename() != '..') {
                $array[$i] = 'App/Resources/Images/Slider/' . $value->getFilename();
                $i++;
            }
        }

        return $array;
    }
}