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

class VendaDetails extends Page
{
    private $activeRecord;
    private $template;

    public function __construct()
    {
        $this->activeRecord = 'Venda';
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $this->template = $twig->load('admin/venda_detalhes.html');
    }

    public function onDetail()
    {
        try {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                Transaction::open();
                $class = $this->activeRecord;
                $object = $class::find($id);
                $replaces['model'] = $object;
                $replaces['opcoes'] = VendaStatusOpcoes::all();
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

    public function onAddSituation()
    {   
        try {
            Transaction::open();

            $vendaStatus = new VendaStatus;
            $vendaStatus->setId_Venda($_POST['id_venda']);
            $vendaStatus->setId_Status($_POST['id_status']);
            $vendaStatus->setData_Hora();
            $vendaStatus->store();

            Transaction::close();
        } catch (Exception $e) {
            Transaction::rollback();
            new Message('error', $e->getMessage());
        }
    }

    public function onDeleteSituation()
    {
        try {
            Transaction::open();

            $vendaStatus = new VendaStatus($_POST['id_status']);
            $vendaStatus->delete();

            Transaction::close();
        } catch (Exception $e) {
            Transaction::rollback();
            new Message('error', $e->getMessage());
        }
    }
}