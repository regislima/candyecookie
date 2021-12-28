<?php

use Framework\Database\Record;

class VendaStatus extends Record
{
    const TABLENAME = 'venda_status';
    private $status;

    public function getStatus()
    {
        if (empty($this->status)) {
            $this->status = new VendaStatusOpcoes($this->getId_Status());
        }

        return $this->status;
    }

    public function getId_venda()
    {
        if (isset($this->data['id_venda'])) {
            return $this->data['id_venda'];
        }
    }

    public function setId_Venda($idVenda)
    {
        $this->data['id_venda'] = $idVenda;
    }

    public function getId_Status()
    {
        if (isset($this->data['id_status'])) {
            return $this->data['id_status'];
        }
    }

    public function setId_Status($idStatus)
    {
        $this->data['id_status'] = $idStatus;
    }

    public function getData_Hora()
    {
        if (isset($this->data['data_hora'])) {
            $data = new DateTime($this->data['data_hora']);
            return $data->format('d/m/Y H:i:s');
        }
    }

    public function setData_Hora()
    {
        $data = new DateTime();
        $this->data['data_hora'] = $data->format('Y-m-d H:i:s');
    }
}