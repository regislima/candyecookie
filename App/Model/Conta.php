<?php

use Framework\Database\Criteria;
use Framework\Database\Record;
use Framework\Database\Repository;

/**
 * Classe que representa os débitos de um cliente
 */
class Conta extends Record {

    const TABLENAME = 'conta';
    private $cliente;
    private $venda;

    public function getCliente() {
        if (empty($this->cliente)) {
            $this->cliente = new Pessoa($this->getId_Cliente());
        }

        return $this->cliente;
    }

    public function getClienteNome() {
        if (empty($this->cliente)) {
            $this->cliente = new Pessoa($this->getId_Cliente());
        }

        return $this->cliente->getNome();
    }

    public function getVenda() {
        if (empty($this->venda)) {
            $this->venda = new Venda($this->getId_Venda());
        }

        return $this->venda;
    }
    
    /**
     * Retorna todas as contas não pagas do cliente
     *
     * @param  int $id_pessoa Id do cliente
     * @return array Array com objetos conta
     */
    public static function getByPessoa($id_pessoa) {
        $criteria = new Criteria;
        $criteria->add('paga', '<>', 'S');
        $criteria->add('id_cliente', '=', $id_pessoa);

        $repo = new Repository('Conta');
        return $repo->load($criteria);
    }
    
    /**
     * Retorna a quantidade de débitos por pessoa
     *
     * @param  int $id_pessoa Id do cliente
     * @return float Total de débitos
     */
    public static function debitosPorPessoa($id_pessoa) {
        $total = 0;
        $contas = self::getByPessoa($id_pessoa);

        if ($contas) {
            foreach ($contas as $conta) {
                $total += $conta->getValor();
            }
        }

        return $total;
    }
    
    /**
     * Registra a quantidade de parcelas e grava no banco de dados
     *
     * @param  int $id_cliente
     * @param  mixed $delay
     * @param  float $valor
     * @param  int $parcelas
     * @return void
     */
    public static function geraParcelas($id_cliente, $id_venda, $delay, $valor, $parcelas) {
        $date = new DateTime(date('Y-m-d'));
        $date->add(new DateInterval('P' . $delay . 'D'));

        for ($n = 1; $n <= $parcelas; $n++) {
            $conta = new self;
            $conta->setId_Cliente($id_cliente);
            $conta->setId_Venda($id_venda);
            $conta->setDt_Emissao(date('Y-m-d'));
            $conta->setDt_Vencimento($date->format('Y-m-d'));
            $conta->setValor($valor / $parcelas);
            $conta->setPaga('N');
            $conta->store();
            $date->add(new DateInterval('P1M'));
        }
    }

    public function getId_Cliente()
    {
        return $this->data['id_cliente'];
    }

    public function setId_Cliente($idCliente)
    {
        $this->data['id_cliente'] = $idCliente;
    }

    public function getDt_Emissao()
    {
        return $this->data['dt_emissao'];
    }

    public function setDt_Emissao($dataEmissao)
    {
        $this->data['dt_emissao'] = $dataEmissao;
    }

    public function getDt_Vencimento()
    {
        return $this->data['dt_vencimento'];
    }

    public function setDt_Vencimento($dataVencimento)
    {
        $this->data['dt_vencimento'] = $dataVencimento;
    }

    public function getValor()
    {
        return $this->data['valor'];
    }

    public function setValor($valor)
    {
        $this->data['valor'] = $valor;
    }

    public function getPaga()
    {
        return $this->data['paga'];
    }

    public function setPaga($paga)
    {
        $this->data['paga'] = $paga;
    }

    public function getId_Venda()
    {
        return $this->data['id_venda'];
    }

    public function setId_Venda($idVenda)
    {
        $this->data['id_venda'] = $idVenda;
    }

    public function __toString()
    {
        return $this->getId();
    }
}
