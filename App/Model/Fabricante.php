<?php

use Framework\Database\Criteria;
use Framework\Database\Record;
use Framework\Database\Repository;

class Fabricante extends Record
{
    const TABLENAME = 'fabricante';

    static public function getFabricanteByName($fabricante) : ?Fabricante
    {
        $criteria = new Criteria;
        $criteria->add('nome', '=', $fabricante);
        
        $repository = new Repository('Fabricante');
        $result = $repository->load($criteria);

        if ($result) {
            return $result[0];
        } else {
            return null;
        }
    }

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

    public function getCNPJ()
    {
        return $this->data['cnpj'];
    }

    public function setCNPJ($cnpj)
    {
        $this->data['cnpj'] = $cnpj;
    }

    public function getUrl()
    {
        return $this->data['url'];
    }

    public function setUrl($url)
    {
        $this->data['url'] = $url;
    }
}