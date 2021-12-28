<?php

use Framework\Database\Record;

class Contato extends Record
{
    const TABLENAME = 'contato';
    private $cliente;

    public function getCliente()
    {
        if (empty($this->cliente)) {
            $this->cliente = new Pessoa($this->getId_cliente());
        }

        return $this->cliente;
    }

    public function getId_Cliente()
    {
        if (isset($this->data['id_cliente'])) {
            return $this->data['id_cliente'];
        }
    }

    public function getAssunto()
    {
        if (isset($this->data['assunto'])) {
            return $this->data['assunto'];
        }
    }

    public function getMensagem()
    {
        if (isset($this->data['mensagem'])) {
            return $this->data['mensagem'];
        }
    }

    public function getData_Hora()
    {
        if (isset($this->data['data_hora'])) {
            $data = new DateTime($this->data['data_hora']);
            return $data->format('d/m/Y H:i:s');
        }
    }

    public function getLida()
    {
        if (isset($this->data['lida'])) {
            return $this->data['lida'];
        }
    }

    public function setId_Cliente($idCliente)
    {
        $this->data['id_cliente'] = $idCliente;
    }

    public function setAssunto(string $assunto)
    {
        $this->data['assunto'] = $assunto;
    }

    public function setMensagem(string $mensagem)
    {
        $this->data['mensagem'] = $mensagem;
    }

    public function setData_Hora()
    {
        $data = new DateTime();
        $this->data['data_hora'] = $data->format('Y-m-d H:i:s');
    }

    public function setLida(String $lida)
    {
        $this->data['lida'] = $lida;
    }

    public function __toString()
    {
        return $this->getId();
    }
}