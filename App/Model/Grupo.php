<?php

use Framework\Database\Record;

class Grupo extends Record {

    const TABLENAME = 'grupo';

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