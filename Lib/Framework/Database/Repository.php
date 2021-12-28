<?php

namespace Framework\Database;

use Exception;

/**
 * Classe responsável por manipular coleções de objetos usando o padrão Repository
 */
final class Repository {
    
    /**
     * Armazena a classe manipulada pelo repositório
     *
     * @var mixed
     */
    private $activeRecord;
    
    /**
     * Inicializa o objeto com a classe alvo da coleção de dados.
     *
     * @param  string $class Classe modelo para coleção de dados.
     * @return void
     */
    public function __construct($class) {
        $this->activeRecord = $class;
    }
    
    /**
     * Método será responsável por carregar na memória uma coleção de objetos.
     *
     * @param  Criteria $criteria Objeto criteria.
     * @return array Coleção de objetos de acordo com os critérios de busca.
     * @throws Exception Conexão com banco de dados não está aberta.
     */
    public function load(Criteria $criteria = null) {
        // Cria a instrução SELECT
        $sql = "select * from " . constant($this->activeRecord . '::TABLENAME');

        // Adiciona a cláusula WHERE a instrução sql
        if ($criteria) {
            $expression = $criteria->dump();
            
            if ($expression) {
                $sql .= ' where ' . $expression;
            }

            // Obtém as propriedades do critério
            $order = $criteria->getProperty('order');
            $limit = $criteria->getProperty('limit');
            $offset = $criteria->getProperty('offset');
            $group = $criteria->getProperty('group');

            // Adiciona a ordenação a instrução sql
            if ($group) {
                $sql .= ' group by ' . $group;
            }
            
            if ($order) {
                $sql .= ' order by ' . $order;
            }

            if ($limit) {
                $sql .= ' limit ' . $limit;
            }

            if ($offset) {
                $sql .= ' offset ' . $offset;
            }
        }

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $result = $conn->query($sql);
            $results = [];

            if ($result) {
                while ($row = $result->fetchObject($this->activeRecord)) {
                    $results[] = $row;
                }
            }

            return $results;
        } else {
            throw new Exception('Não há transação ativa.');
        }
    }

    /**
     * Método será responsável por deletar uma coleção de objetos.
     *
     * @param  Criteria $criteria Objeto criteria.
     * @return int|FALSE Retorna a quantidade de linhas afetadas em caso de sucesso. False caso contrário.
     * @throws Exception Conexão com banco de dados não está aberta.
     */
    public function delete(Criteria $criteria) {
        $expression = $criteria->dump();
        $sql = 'delete from ' . constant($this->activeRecord . '::TABLENAME');

        if ($expression) {
            $sql .= ' where ' . $expression;
        }

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $result = $conn->exec($sql);
            return $result;
        } else {
            throw new Exception('Não há transação ativa.');
        }
    }
    
    /**
     * Método que irá contar quantos objetos satisfazem a um dado critério.
     *
     * @param  mixed $criteria Objeto criteria.
     * @return int Número de registros encontrados.
     * @throws Exception Conexão com banco de dados não está aberta.
     */
    public function count(Criteria $criteria) {
        $expression = $criteria->dump();
        $sql = 'select count(*) from ' . constant($this->activeRecord . '::TABLENAME');

        if ($expression) {
            $sql .= ' where ' . $expression;
        }

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $result = $conn->query($sql);
            
            if ($result) {
                $row = $result->fetch();
            }

            return $row[0];
        } else {
            throw new Exception('Não há transação ativa.');
        }
    }

    /**
     * Executa um comando sql diretamente sem formatação.
     * 
     * @param string $sql Instrução SQL.
     * @return mixed Retorna o número de linas afetadas. Falso caso contrário.
     * @throws Exception Não há transação aberta.
     */
    public function directSQL(string $sql)
    {
        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $result = $conn->query($sql);
            $results = [];

            if ($result) {
                while ($row = $result->fetchObject($this->activeRecord)) {
                    $results[] = $row;
                }
            }

            return $results;
        } else {
            throw new Exception('Não há transação ativa.');
        }
    }
}