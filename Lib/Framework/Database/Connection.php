<?php

namespace Framework\Database;

use Exception;
use PDO;

/**
 * Classe que implementa o padrão Factory, cujo papel será instanciar
 * um objeto PDO de acordo com as informações de contidas no arquivo
 * de configuração.
 */
class Connection {
    
    // Construtor privado para evitar instanciação da classe
    private function __construct() {}
        
    /**
     * Prover objetos PDO
     * 
     * Banco de dados suportados
     *      - MySQL
     *      - PostegreSQL
     *      - IBase
     *      - Microsoft SQLServer
     *      - SqLite
     *      - OCI8
     * @param  String $name Nome do arquivo de configuração com informações
     *                      do banco de dados
     * @return PDO Instância da conexão PDO
     */
    public static function open() {
        if (file_exists("App/Config/conf.ini")) {
            $db = parse_ini_file("App/Config/conf.ini");
        } else {
            throw new Exception('Arquivo conf.ini não encontrado');
        }

        // Lê as informação odo arquivo
        // Info de conexão
        $user = isset($db['user']) ? $db['user'] : null;
        $pass = isset($db['pass']) ? $db['pass'] : null;
        $name = isset($db['name']) ? $db['name'] : null;
        $host = isset($db['host']) ? $db['host'] : null;
        $type = isset($db['type']) ? $db['type'] : null;
        $port = isset($db['port']) ? $db['port'] : null;

        // Descobre qual o tipo de driver deve ser utilizado para a conexão
        switch ($type) {
            case 'mysql':
                $port = $port ? $port : '3306';
                $conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
                break;
            
            case 'pgsql':
                $port = $port ? $port : '5432';
                $conn = new PDO("pgsql:dbname={$name};user={$user};password={$pass};host={$host};port={$port}");
                break;
            
            case 'mssql':
                $port = $port ? $port : '1433';
                $conn = new PDO("dblib:host={$host},{$port};dbname={$name}", $user, $pass);
                break;
            
            case 'sqlite':
                $conn = new PDO("sqlite:{$name}");
                $conn->exec('PRAGMA foreign_keys = ON');
                break;
            
            case 'ibase':
                $conn = new PDO("firebird:dbname={$name}", $user, $pass);
                break;
            
            case 'oci8':
                $conn = new PDO("oci:dbname={$name}", $user, $pass);
                break;
        }

        // Define para que o PDO lance exceções na ocorrência de errros
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}