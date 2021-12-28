<?php

use Framework\Database\Record;

class ItemVenda extends Record {

    const TABLENAME = 'item_venda';
    private $produto;

    public function getProduto() {
        if (empty($this->produto)) {
            $this->produto = new Produto($this->getId_Produto());
        }

        return $this->produto;
    }

    public function getId_Produto()
    {
        return $this->data['id_produto'];
    }

    public function setId_Produto($idProduto)
    {
        $this->data['id_produto'] = $idProduto;
    }

    public function getId_Venda()
    {
        return $this->data['id_venda'];
    }

    public function setId_Venda($idVenda)
    {
        $this->data['id_venda'] = $idVenda;
    }

    public function setQuantidade($quantidade)
    {
        $this->data['quantidade'] = $quantidade;
    }

    public function getQuantidade()
    {
        return $this->data['quantidade'];
    }

    public function setPreco_Unitario($preco)
    {
        $this->data['preco_unitario'] = $preco;
    }

    public function getPreco_Unitario()
    {
        return $this->data['preco_unitario'];
    }

    public function setPreco_Total($precoTotal)
    {
        $this->data['preco_total'] = $precoTotal;
    }
    
    public function getPreco_Total()
    {
        return $this->data['preco_total'];
    }
}
