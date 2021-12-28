<?php

use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ProdutoDetails extends Page
{
    private $activeRecord;
    private $template;

    public function __construct()
    {
        $this->activeRecord = 'Produto';
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $this->template = $twig->load('admin/produto_profile.html');
    }

    public function onDetail($param)
    {
        try {
            if (isset($param['id'])) {
                Transaction::open();
                $class = $this->activeRecord;
                $object = $class::find($param['id']);
                $replaces['model'] = $object;
                $model = $this->template->render($replaces);
                Transaction::close();

                parent::add($model);
            }
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }

    public function show()
    {
        parent::show();
    }
}
