<?php

namespace Framework\Traits;

use Exception;
use Framework\Database\Transaction;
use Framework\Widgets\Dialog\Message;

/**
 * Trait para gravar cadastros no banco de dados
 */
trait SaveTrait
{
    function onSave() {
        try {
            Transaction::open();

            $class = $this->activeRecord;
            $dados = $this->form->getData($class);

            if (is_object($dados)) {
                $dados->store();
                new Message('info', 'Dados armazenados com sucesso');
                Transaction::close();
                return true;
            } else {
                new Message('info', $dados);
            }
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
    }
}
