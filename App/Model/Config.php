<?php

use Framework\Database\Record;

class Config extends Record
{
    const TABLENAME = 'config';

    public function getValor_Frete()
    {
        if (isset($this->data['valor_frete'])) {
            return $this->data['valor_frete'];
        }
    }

    public function getValor_Minimo_Frete()
    {
        if (isset($this->data['valor_minimo_frete'])) {
            return $this->data['valor_minimo_frete'];
        }
    }

    public function setValor_Frete($valorFrete)
    {
        if ($valorFrete < 0) {
            return 'Frete com valor inválido';
        } else {
            $this->data['valor_frete'] = $valorFrete;
        }
    }

    public function setValor_Minimo_Frete($valorMinimoFrete)
    {
        if ($valorMinimoFrete < 0) {
            return 'Valor inválido';
        } else {
            $this->data['valor_minimo_frete'] = $valorMinimoFrete;
        }
    }
}