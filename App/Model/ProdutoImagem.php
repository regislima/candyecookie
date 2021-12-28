<?php

use Framework\Database\Record;

class ProdutoImagem extends Record
{
    const TABLENAME = 'produto_imagem';
    private $produto;

    public function getProduto()
    {
        if (empty($this->produto)) {
            $this->produto = new Produto($this->getId());
        }

        return $this->produto;
    }

    public function getId_Produto()
    {
        if (isset($this->data['id_produto'])) {
            return $this->data['id_produto'];
        }
    }

    public function getImagem()
    {
        if (isset($this->data['imagem'])) {
            return $this->data['imagem'];
        }
    }

    public function setId_Produto($idProduto)
    {
        $this->data['id_produto'] = $idProduto;
    }

    public function setImagem($imagem)
    {
        $this->data['imagem'] = $imagem;
 
    }

    public function __toString()
    {
        return $this->getImagem();
    }
}