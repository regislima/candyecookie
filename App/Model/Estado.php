<?php

use Framework\Database\Record;

class Estado extends Record {

    const TABLENAME = 'estado';

    public function __toString()
    {
        return $this->getNome();
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