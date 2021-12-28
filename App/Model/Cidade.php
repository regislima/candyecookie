<?php

use Framework\Database\Criteria;
use Framework\Database\Record;
use Framework\Database\Repository;
use Framework\Database\Transaction;

class Cidade extends Record
{
    const TABLENAME = 'cidade';
    private $estado;
  
    /**
     * Retorna o estado que a cidade pertence.
     *
     * @return object Objeto Estado
     */
    public function getEstado() {
        if (empty($this->estado)) {
            $this->estado = new Estado($this->getId_Estado());
        }

        return $this->estado;
    }

    static public function getCidadeByName($cidade) : Cidade
    {
        $criteria = new Criteria;
        $criteria->add('nome', '=', $cidade);
        $repository = new Repository('Cidade');
        $result = $repository->load($criteria);

        return $result[0];
    }

    public function getNome()
    {
        if ($this->data['nome']) {
            return $this->data['nome'];
        }
    }

    public function setNome($nome)
    {
        $tmp = FieldValidatorHelper::validaNome($nome);

        if ($tmp) {
            return $tmp;
        }

        $this->data['nome'] = $nome;
    }

    public function getId_Estado()
    {
        if ($this->data['id_estado']) {
            return $this->data['id_estado'];
        }
    }

    public function setId_Estado($idEstado)
    {
        $tmp = FieldValidatorHelper::validaId($idEstado);

        if ($tmp) {
            return $tmp;
        }

        $this->data['id_estado'] = $idEstado;
    }

    public function __toString()
    {
        return $this->getNome();
    }
}
