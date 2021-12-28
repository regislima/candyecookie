<?php

namespace Framework\Traits;

use Exception;
use Framework\Control\Action;
use Framework\Database\Transaction;
use Framework\Log\LoggerTXT;
use Framework\Widgets\Dialog\Message;
use Framework\Widgets\Dialog\Question;
use ImagemHelper;

/**
 * Deleta registros da base de dados
 */
trait DeleteTrait
{
    function onDelete($param) {
        try {
            Transaction::open();

            $id = $param['id'];
            $object = $this->activeRecord::find($id);
            Transaction::close();

            $action1 = new Action(array($this, 'delete'));
            $action1->setParameter('id', $id);
            $action2 = new Action(array($this, 'onReload'));
            new Question("Deseja realmente excluir o registro \"{$object}\"?", $action1, $action2);
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }

    function delete($param) {
        try {
            Transaction::open();

            $id = $param['id'];
            $object = $this->activeRecord::find($id);
            $object->delete();

            // Removendo a imagem do diretório
            if (method_exists($object, 'getImagem') and $object->getImagem()) {
                ImagemHelper::remove($object->getImagem());
            }

            Transaction::close();
            $this->onReload();
            new Message('info', 'Registro excluido com sucesso.');
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }
}
