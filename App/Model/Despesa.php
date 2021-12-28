<?php

use Framework\Database\Record;
use Framework\Database\Transaction;

class Despesa extends Record
{
    const TABLENAME = 'despesa';
    private $empresa;
    private $formaPagamento;

    public function getEmpresa()
    {
        if (empty($this->empresa)) {
            $this->empresa = new Fabricante($this->getId_Empresa());
        }

        return $this->empresa;
    }

    public function getEmpresaNome()
    {
        if (empty($this->empresa)) {
            $this->empresa = new Fabricante($this->getId_Empresa());
        }

        return $this->empresa->getNome();
    }

    public function getFormaPagamento()
    {
        if (empty($this->formaPagamento)) {
            $this->formaPagamento = new FormaPagamento($this->getId_Forma_Pagamento());
        }

        return $this->formaPagamento;
    }

    public function getFormaPagamentoNome()
    {
        if (empty($this->formaPagamento)) {
            $this->formaPagamento = new FormaPagamento($this->getId_Forma_Pagamento());
        }

        return $this->formaPagamento->getForma();
    }

    public function getId_Empresa()
    {
        if (isset($this->data['id_empresa'])) {
            return $this->data['id_empresa'];
        }
    }

    public function getData_Despesa()
    {
        if (isset($this->data['data_despesa'])) {
            return $this->data['data_despesa'];
        }
    }

    public function getValor()
    {
        if (isset($this->data['valor'])) {
            return $this->data['valor'];
        }
    }

    public function getId_Forma_Pagamento()
    {
        if (isset($this->data['id_forma_pagamento'])) {
            return $this->data['id_forma_pagamento'];
        }
    }

    public function getDescricao()
    {
        if (isset($this->data['descricao'])) {
            return $this->data['descricao'];
        }
    }

    public function getDias_Cheque()
    {
        if (isset($this->data['dias_cheque'])) {
            return $this->data['dias_cheque'];
        }
    }

    public function setId_Empresa($idEmpresa)
    {
        if ($idEmpresa) {
            $objFab = Fabricante::getFabricanteByName($idEmpresa);

            if ($objFab) {
                $tmp = FieldValidatorHelper::validaId($objFab->getId());
            } else {
                $tmp = 'Empresa não cadastrado.';
            }
            
            if ($tmp) {
                return $tmp;
            }

            $this->data['id_empresa'] = $objFab->getId();
        } else {
            return 'Campo "Empresa" é obrigatório.';
        }
    }

    public function setData_Despesa($dataDespesa)
    {
        if ($dataDespesa) {
            $this->data['data_despesa'] = $dataDespesa;
        } else {
            return 'Campo "Data da Despesa" é obrigatório.';
        }
        
    }

    public function setValor($valor)
    {
        if ($valor) {
            if ($valor >= 0) {
                $this->data['valor'] = FieldValidatorHelper::moeda_para_banco($valor);
            } else {
                return 'Campo "Valor" inválido.';
            }
        } else {
            return 'Campo "Valor" é obrigatório.';
        }
    }

    public function setId_Forma_Pagamento($idFormaPagamento)
    {
        if (intval($idFormaPagamento) > 0) {
            $this->data['id_forma_pagamento'] = $idFormaPagamento;
        } else {
            return 'Campo "Forma de Pagamento" é obrigatório.';
        }
    }

    public function setDescricao($descricao)
    {
        if ($descricao) {
            if (strlen($descricao) < 4) {
                $this->data['descricao'] = $descricao;
            } else {
                return 'Campo "Descrição" com poucos caracteres.';
            }
        } else {
            return 'Campo "Descrição" é obrigatório.';
        }
    }

    public function setDias_Cheque($diasCheque)
    {
        if (intval($diasCheque) >= 0) {
            $this->data['dias_cheque'] = $diasCheque;
        } else {
            return 'Campo "Dias" com valor inválido.';
        }
    }

    public function __toString()
    {
        return $this->getId();
    }

    public static function getDespesaMes()
    {
        $meses = [];
        $meses[1] = 'Jan';
        $meses[2] = 'Fev';
        $meses[3] = 'Mar';
        $meses[4] = 'Abr';
        $meses[5] = 'Mai';
        $meses[6] = 'Jun';
        $meses[7] = 'Jul';
        $meses[8] = 'Ago';
        $meses[9] = 'Set';
        $meses[10] = 'Out';
        $meses[11] = 'Nov';
        $meses[12] = 'Dez';
        
        $conn = Transaction::get();
        $result = $conn->query("select date_format(data_despesa, '%m') as mes, sum(valor) as valor from despesa where date_format(data_despesa, '%Y') = date('Y') group by 1");
        
        $dataset = [
            'Jan' => 0.00,
            'Fev' => 0.00,
            'Mar' => 0.00,
            'Abr' => 0.00,
            'Mai' => 0.00,
            'Jun' => 0.00,
            'Jul' => 0.00,
            'Ago' => 0.00,
            'Set' => 0.00,
            'Out' => 0.00,
            'Nov' => 0.00,
            'Dez' => 0.00,
        ];
        
        foreach ($result as $row)
        {
            $mes = $meses[(int) $row['mes']];
            $dataset[$mes] = intVal($row['valor']);
        }
        
        return $dataset;
    }
}