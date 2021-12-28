<?php

use Framework\Database\Record;

class Tipo extends Record {

    const TABLENAME = 'tipo';

    public function __toString()
    {
        return $this->getNome();   
    }

    public function getNome()
    {
        return $this->data['nome'];
    }

    public function setNome($nome)
    {
        $this->data['nome'] = $nome;
    }
}