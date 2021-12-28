<?php

namespace Framework\Traits;

use Exception;
use Framework\Database\Transaction;
use Framework\Widgets\Dialog\Message;

/**
 * Trait para edição de cadastros
 */
trait EditTrait
{
    function onEdit($param) {
        try {
            if (isset($param['id'])) {
                Transaction::open();

                $id = $param['id'];
                $class = $this->activeRecord;
                $object = $class::find($id);
                $this->form->setData($object);

                Transaction::close();
            }
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
    }
}
