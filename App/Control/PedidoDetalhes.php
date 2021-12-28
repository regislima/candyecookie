<?php

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class PedidoDetalhes extends Page
{
    private $activeRecord;
    private $template;

    public function __construct()
    {
        $this->activeRecord = 'Venda';
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $this->template = $twig->load('user_pedido_detalhes.html');
    }

    public function onDetail()
    {
        try {
            if (isset($_GET['id'])) {
                Transaction::open();
                $class = $this->activeRecord;
                $object = $class::find($_GET['id']);
                $replaces['model'] = $object;
                $replaces['qrcode'] = $this->qrCode($object);
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

    private function qrCode($object) {
        $renderer = new ImageRenderer(new RendererStyle(100), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        return $writer->writeString($object->getId() . ' : ' . $object->getValor_Final());
    }
}