<?php

use Framework\Database\Record;

class FormaPagamento extends Record
{
    const TABLENAME = 'forma_pagamento';

    public function getForma()
    {
        if (isset($this->data['forma'])) {
            return $this->data['forma'];
        }
    }

    public function setForma($forma)
    {
        if ($forma) {
            $this->data['forma'] = $forma;
        } else {
            return 'Informe a forma de pagamento.';
        }
    }
}