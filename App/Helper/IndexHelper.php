<?php

use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;

class IndexHelper
{
    static public function ContatoNaoLido()
    {
        $criteria = new Criteria;
        $criteria->add('lida', '=', 'N');

        $repo = new Repository('Contato');
        $contatos = $repo->load($criteria);

        return $contatos;
    }

    static public function VendasRecebidas()
    {
        $criteria = new Criteria;
        $criteria->add('id_status', '=', 1);
        $criteria->setProperty('limit', 10);

        $repo = new Repository('VendaStatus');
        $vendas = $repo->directSQL('SELECT * FROM venda_status GROUP BY id_venda HAVING count(id_venda) = 1');

        return $vendas;
    }
}