<?php

namespace Framework\Log;

/**
 * Classe abstrata para criação de logs
 */
abstract class Logger {

    protected $filename;
    
    /**
     * Construtor da classe Logger
     *
     * @param  String $filename Nome do arquivo de log
     */
    public function __construct($filename) {
        $conf = parse_ini_file("App/Config/conf.ini");
        
        // Info de gravação de log
        $log_file_path = isset($conf['log_file_path']) ? $conf['log_file_path'] : null;

        $this->filename = $log_file_path . $filename;
    }

    public abstract function write($message);
}