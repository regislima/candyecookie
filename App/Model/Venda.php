<?php

use Framework\Database\Criteria;
use Framework\Database\Record;
use Framework\Database\Repository;
use Framework\Database\Transaction;

class Venda extends Record {

    const TABLENAME = 'venda';
    private $itens;
    private $cliente;
    private $formaPagamento;
    private $vendaStatus;
    
    /**
     * Retorna o cliente que participou da venda
     *
     * @return Pessoa Pessoa da venda
     */
    public function getCliente() {
        if (empty($this->cliente)) {
            $this->cliente = new Pessoa($this->getId_Cliente());
        }

        return $this->cliente;
    }
    
    /**
     * Retorna a forma de pagamento da venda
     *
     * @return FormaPagamento Forma de pagamento da venda
     */
    public function getFormaPagamento() {
        if (empty($this->formaPagamento)) {
            $this->formaPagamento = new FormaPagamento($this->getId_Forma_Pagamento());
        }

        return $this->formaPagamento;
    }

    /**
     * Retorna o status da venda (Recebido, Em Transporte, Entregue).
     *
     * @return array Todos os status de uma venda
     */
    public function getVenda_Status()
    {
        $repositorio = new Repository('VendaStatus');
        $criterio = new Criteria;
        $criterio->add('id_venda', '=', $this->getId());
        $this->vendaStatus = $repositorio->load($criterio);
        return $this->vendaStatus;
    }
    
    /**
     * Adiciona um produto a venda
     *
     * @param  Produto $p Objeto produto
     * @param  int $quantidade Quantidade de produtos que serão adicionados
     * @return void
     */
    public function addItem($item) {
        $itemVenda = new ItemVenda;
        $itemVenda->setId_Produto($item->id);
        $itemVenda->setQuantidade($item->quantidade);
        $itemVenda->setPreco_Unitario($item->preco_venda);
        $itemVenda->setPreco_Total($item->preco_venda * $item->quantidade);
        
        $this->itens[] = $itemVenda;
    }
    
    /**
     * Grava no banco de dados a venda e seus itens
     *
     * @return void
     */
    public function store() {
        parent::store();

        foreach ($this->itens as $item) {
            $item->setId_Venda($this->getId());
            $item->store();
        }
    }
    
    /**
     * Retorna os todos os itens da venda
     *
     * @return array Itens da venda
     */
    public function getItens() {
        $repositorio = new Repository('ItemVenda');
        $criterio = new Criteria;
        $criterio->add('id_venda', '=', $this->getId());
        $this->itens = $repositorio->load($criterio);
        return $this->itens;
    }

    public function getId_Cliente()
    {
        return $this->data['id_cliente'];
    }

    public function setId_Cliente($id)
    {
        $this->data['id_cliente'] = $id;
    }

    public function getData_Venda()
    {
        if (isset($this->data['data_venda'])) {
            return FieldValidatorHelper::data_para_exibir($this->data['data_venda']);
        }
    }

    public function setData_Venda($dataVenda)
    {
        $this->data['data_venda'] = FieldValidatorHelper::data_para_banco($dataVenda);
    }

    public function getParcelas()
    {
        return $this->data['parcelas'];
    }

    public function setParcelas($parcelas)
    {
        $this->data['parcelas'] = $parcelas;
    }

    public function getSubtotal()
    {
        return $this->data['subtotal'];
    }

    public function setSubtotal($subTotal)
    {
        $this->data['subtotal'] = $subTotal;
    }

    public function getDesconto()
    {
        if (isset($this->data['desconto'])) {
            return $this->data['desconto'];
        }
    }

    public function setDesconto($desconto)
    {
        $this->data['desconto'] = $desconto;
    }

    public function getAcrescimos()
    {
        if (isset($this->data['acrescimos'])) {
            return $this->data['acrescimos'];
        }
    }

    public function setAcrescimos($acrescimos)
    {
        $this->data['acrescimos'] = $acrescimos;
    }

    public function getValor_Final()
    {
        return $this->data['valor_final'];
    }

    public function setValor_Final($valorFinal)
    {
        $this->data['valor_final'] = $valorFinal;
    }

    public function getPresente()
    {
        if (isset($this->data['presente'])) {
            $this->data['presente'];
        }
    }

    public function setPresente($presente)
    {
        $this->data['presente'] = $presente;
    }

    public function getId_Forma_Pagamento()
    {
        if (isset($this->data['id_forma_pagamento'])) {
            return $this->data['id_forma_pagamento'];
        }
    }

    public function setId_Forma_Pagamento($formaPagamento)
    {
        $this->data['id_forma_pagamento'] = $formaPagamento;
    }

    public function __toString()
    {
        return $this->data['id'];
    }

    public static function getVendasMes()
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
        $result = $conn->query("select date_format(data_venda, '%m') as mes, sum(valor_final) as valor from venda where date_format(data_venda, '%Y') = date('Y') group by 1");
        
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
            $mes = $meses[ (int) $row['mes']];
            $dataset[$mes] = intVal($row['valor']);
        }
        
        return $dataset;
    }
}
