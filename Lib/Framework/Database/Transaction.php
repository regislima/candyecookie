<?php

namespace Framework\Database;

use Framework\Log\Logger;

/**
 * Classe responsável pela interação entre a aplicação e o banco de dados
 */
final class Transaction {

    private static $conn;
    private static $logger;

    private function __construct() {}
    
    /**
     * Abre uma conexão com o banco de dados
     *
     * @param  String $database Nome do arquivo de configuração com informações
     *                  do banco de dados.
     * @return void
     */
    public static function open() {
        if (empty(self::$conn)) {
            self::$conn = Connection::open();
            self::$conn->beginTransaction();
            self::$logger = null;
        }
    }
    
    /**
     * Retorna a conexão com o banco de dados
     *
     * @return $conn
     */
    public static function get() {
        return self::$conn;
    }
    
    /**
     * Desfaz todas as operações executadas desde o início da transação.
     *
     * @return void
     */
    public static function rollback() {
        if (self::$conn) {
            self::$conn->rollback();
            self::$conn = null;
        }
    }
    
    /**
     * Fecha a conexão com o banco de dados e aplica as alterações realizadas desde
     * o inicio da transação.
     *
     * @return void
     */
    public static function close() {
        if (self::$conn) {
            self::$conn->commit();
            self::$conn = null;
        }
    }
    
    /**
     * Armazena uma instância de Logger
     *
     * @param  Logger $logger Instância filha da classe Logger
     * @return void
     */
    public static function setLogger(Logger $logger) {
        self::$logger = $logger;
    }
    
    /**
     * Registra a mensagem
     *
     * @param  String $message Mensagem a ser registrada
     * @return void
     */
    public static function log($message) {
        if (self::$logger) {
            self::$logger->write($message);
        }
    }
}