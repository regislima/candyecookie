<?php

use Framework\Database\Record;

class Unidade extends Record {

    const TABLENAME = 'unidade';

    public function __toString()
    {
        return $this->getSigla();   
    }

    public function getSigla()
    {
        return $this->data['sigla'];
    }

    public function setSigla($sigla)
    {
        $this->data['sigla'] = $sigla;
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