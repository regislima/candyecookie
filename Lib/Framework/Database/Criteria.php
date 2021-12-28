<?php

namespace Framework\Database;

/**
 * Classe que servirá de suporte para representar expressões de critério de filtro de dados por 
 * meio de um mecanismo totalmente orientado a objetos.
 */
class Criteria {
    
    /**
     * Armazena a lista de filtros
     *
     * @var array
     */
    private $filters;

    public function __construct() {
        $this->filters = [];
    }
    
    /**
     * Adiciona regra de filtro ao objeto.
     *
     * @param  mixed $variable
     * @param  string $compare_operator Operadores de comparação.
     * @param  mixed $value
     * @param  string $logic_operator Operador lógico. Caso não seja informado nenhum valor, 
     *                  será atribuido por padão o valor 'AND'.
     *                  Possíveis valores:
     *                  - 'and', 'or'.
     * 
     * @return void
     */
    public function add($variable, $compare_operator, $value, $logic_operator = 'and') {
        if (empty($this->filters)) {
            $logic_operator = null;
        }

        $this->filters[] = [$variable, $compare_operator, $this->transform($value), $logic_operator];
    }
    
    /**
     * Transforma tipos de dados do PHP em tipos de dados suportados pelo banco de dados
     *
     * @param  mixed $value Valor para formatação. Possíveis valores:
     *                  - Variáveis escalares
     *                  - Arrays unidimensionais
     * @return string $result String plana com valores formatados
     */
    private function transform($value) {
        if (is_array($value)) {
            foreach ($value as $x) {
                if (is_integer($x)) {
                    $foo[] = $x;
                }

                if (is_string($x)) {
                    $foo[] = "'$x'";
                }
            }

            // Converte o array 'foo' em uma string separada por ','
            $result = '(' . implode(',', $foo) . ')';
        
        } else if (is_string($value)) {
            $result = "'$value'";
        
        } else if (is_null($value)) {
            $result = 'null';
        
        } else if (is_bool($value)) {
            $result = $value ? 'TRUE' : 'FALSE';
        
        } else {
            $result = $value;
        }

        return $result;
    }
    
    /**
     * O método responsável por retornar estas regras de filtro em formato de string simples, 
     * para que possa então ser utilizado dentro de um comando SQL (SELECT, UPDATE, DELETE).
     *
     * @return string String SQL.
     */
    public function dump() {
        // Concatena a lista de expressões
        if (is_array($this->filters) and count($this->filters) > 0) {
            $result = '';
            
            foreach ($this->filters as $filter) {
                $result .= $filter[3] . ' ' . $filter[0] . ' ' . $filter[1] . ' ' . $filter[2] . ' ';
            }

            $result = trim($result);
            return "({$result})";
        }
    }
    
    /**
     * Método usdado para informar outras propriedades do critério, tais como order, limit, offset.
     *
     * @param  string $property Propriedades do critério, tais como order, limit, offset.
     * @param  mixed $value tipo de ordenação (id, nome, etc).
     * @return void
     */
    public function setProperty($property, $value) {
        if (isset($value)) {
            $this->properties[$property] = $value;
        } else {
            $this->properties[$property] = null;
        }
    }
    
    /**
     * Método usdado para buscar propriedades do critério, tais como order, limit, offset.
     *
     * @param  string $property Propriedades do critério.
     * @return string Valor da propriedade buscada.
     */
    public function getProperty($property) {
        if (isset($this->properties[$property])) {
            return $this->properties[$property];
        }
    }
}