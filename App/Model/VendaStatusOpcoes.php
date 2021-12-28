<?php

use Framework\Database\Record;

class VendaStatusOpcoes extends Record
{
    const TABLENAME = 'venda_status_opcoes';

    public function getStatus()
    {
        if (isset($this->data['status'])) {
            return $this->data['status'];
        }
    }

    public function setStatus($status)
    {
        $this->data['status'] = $status;
    }

    public function __toString()
    {
        return $this->getStatus();
    }
}