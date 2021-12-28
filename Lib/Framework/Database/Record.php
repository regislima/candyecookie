<?php

namespace Framework\Database;

use Exception;
use PDO;

/**
 * Classe abstrata com funções para manipulação de um objeto com o banco de dados.
 */
abstract class Record {
    
    /**
     * array contendo as propriedades do objeto.
     *
     * @var mixed
     */
    protected $data;

    public function __construct($id = null) {
        if ($id) {
            $object = $this->load($id);
            
            if ($object) {
                $this->fromArray($object->toArray());
            }
        }
    }
    
    /**
     * Retorna o id do objeto
     *
     * @return id Id do objeto
     */
    public function getId()
    {
        if (isset($this->data['id'])) {
            return $this->data['id'];
        }
        
        return null;
    }

    /**
     * Atribui id ao objeto
     *
     * @return void
     */
    public function setId($id)
    {
        $this->data['id'] = $id;
    }
    
    /**
     * Método que é executado sempre que um Active Record for clonado.
     * Automaticamente remove o id do objeto clonado para evitar inconsitências.
     *
     * @return void
     */
    public function __clone() {
        unset($this->data['id']);
    }

    /**
     * Sempre que um valor for atribuído a uma propriedade do objeto, o método __set() será executado,
     * interceptando essa atribuição, pois a propriedade em questão não estará definida. O valor a ser 
     * atribuído será armazenado no array $data, indexado pelo nome da propriedade.
     *
     * @param  mixed $prop Indice do array de propriedades
     * @param  mixed $value Valor a ser atribuído no vetor
     * @return void
     */
    public function __set($prop, $value) {
        if ($value == null) {
            unset($this->data[$prop]);
        } else {
            $this->data[$prop] = $value;
        }
    }
    
    /**
     * Esse método é responsável por retornar o nome da tabela na qual o Active Record será persistido. Para 
     * isso, ele verifica a ocorrência de uma constante de classe chamada TABLENAME na classe Active Record.
     *
     * @return String Nome da tabela de persistência.
     */
    private function getEntity() {
        $class = get_class($this);
        return constant("{$class}::TABLENAME");
    }
    
    /**
     * O método fromArray() será usado para preencher os atributos de um Active Record com os dados de 
     * um array, de modo que os índices desse array são os atributos do objeto.
     *
     * @param  mixed $data
     * @return void
     */
    public function fromArray($data) {
        $this->data = $data;
    }
    
    /**
     * O método toArray() será utilizado para retornar todos os atributos de um objeto em forma de array.
     *
     * @return void
     */
    public function toArray() {
        return $this->data;
    }
    
    /**
     * Método responsável por persistir um objeto no banco de dados.
     *
     * @return int|FALSE Retorna a quantidade de linhas afetadas ou FALSE caso contrário.
     * @throws Exception Conexão com banco de dados não está aberta. 
     */
    public function store() {
        $prepared = $this->prepare($this->data);
        
        if (empty($this->data['id'])) {
            $this->data['id'] = $this->getLast() + 1;
            $id = ['id' => $this->data['id']];
            $prepared = $id + $prepared;
            $sql = "insert into {$this->getEntity()} " .
                '(' . implode(', ', array_keys($prepared)) . ')' .
                ' values' .
                '(' . implode(', ', array_values($prepared)) . ')';
        } else {
            $sql = "update {$this->getEntity()}";
            
            if ($prepared) {
                foreach ($prepared as $colunm => $value) {
                    if ($colunm !== 'id') {
                        $set[] = "{$colunm} = {$value}";
                    }
                }
            }

            $sql .= ' set ' . implode(', ', $set);
            $sql .= ' where id = ' . (int) $this->data['id'];
        }

        if ($conn = Transaction::get()) {
            Transaction::log($sql);            
            return $conn->exec($sql);

        } else {
            throw new Exception("Não há transação ativa.");
        }
    }
    
    /**
     * Método responsável por ler um registro do banco de dados e retorná-lo na forma de um objeto.
     *
     * @param  int $id Identificador do objeto
     * @return object Dados retornados na forma de objeto
     * @throws Exception Conexão com banco de dados não está aberta.
     */
    public function load($id) {
        $sql = "select * from {$this->getEntity()} where id = :id";

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            if ($result) {
                $object = $stmt->fetchObject(get_class($this));
            }

            return $object;
        } else {
            throw new Exception("Não há transação ativa.");
        }
    }
    
    /**
     * Método responsável por excluir o objeto atual da base de dados e poderá receber opcionalmente
     * um $id como parâmetro. Neste caso, assumirá esse $id a ser excluído, mas o comportamento-padrão
     * é excluir o objeto atual.
     *
     * @return int|FALSE Retorna a quantidade de linhas afetadas ou FALSE caso contrário.
     * @throws Exception Conexão com banco de dados não está aberta. 
     */
    public function delete($id = null) {
        $id = $id ? $id : $this->data['id'];
        $sql = "delete from {$this->getEntity()} where id = :id";

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } else {
            throw new Exception("Não há transação ativa.");
        }
    }
    
    /**
     * Método utilizado para buscarmos um objeto a partir da base de dados. Ele na verdade é só um
     * atalho para o método load(), com a facilidade de ser executado estaticamente.
     *
     * @param  int $id Identificador do objeto
     * @return object Dados retornados na forma de objeto
     */
    public static function find($id) {
        $classname = get_called_class();
        $ar = new $classname;
        return $ar->load($id);
    }
    
    /**
     * Retorna todos os registros do objeto chamado
     *
     * @return array
     */
    public static function all() {
        $class = get_called_class();
        $repo = new Repository($class);
        return $repo->load();
    }
    
    /**
     * Retornar o último ID armazenado na tabela.
     *
     * @return int Último id armazenado na tabela.
     * @throws Exception Conexão com banco de dados não está aberta.
     */
    public function getLast() {
        $sql = "select max(id) from {$this->getEntity()}";

        if ($conn = Transaction::get()) {
            Transaction::log($sql);
            $result = $conn->query($sql);
            $row = $result->fetch();
            return $row[0];
        } else {
            throw new Exception("Não há transação ativa.");
        }
    }
    
    /**
     * Método que prepara os dados antes de serem inseridos na base de dados.
     *
     * @param  mixed $data Array de valores.
     * @return array Array com valores formatados para inserção no banco de dados.
     */
    private function prepare($data) {
        $prepared = [];
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $prepared[$key] = $this->escape($value);
            }
        }

        return $prepared;
    }
    
    /**
     * Método que recebe um valor e formata conforme seu tipo:
     *  - String: adiciona '' (aspas simples) nos valores strings e date;
     *  - Boolean: retorna True ou False
     *
     * @param  mixed $value Valor para ser formatado.
     * @return mixed Valor formatado.
     */
    private function escape($value) {
        if (is_string($value) and !empty($value)) {
            $value = addslashes($value);
            return "'$value'";
        } else if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } else if ($value !== '') {
            return $value;
        } else {
            return 'null';
        }
    }
}