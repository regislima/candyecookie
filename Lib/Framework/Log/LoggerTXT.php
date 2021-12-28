<?php

namespace Framework\Log;

/**
 * Classe concreta que cria logs no formato txt.
 */
class LoggerTXT extends Logger {
    
    /**
     * Cria o arquivo de log no formato txt
     *
     * @param  String $message Conteúdo do arquivo de log
     * @return void
     */
    public function write($message) {
        date_default_timezone_set('America/Fortaleza');
        $time = date('Y-m-d H:i:s');
        $text = "$time :: $message\n";
        $handler = fopen($this->filename . '.txt', 'a');
        fwrite($handler, $text);
        fclose($handler);
    }
}
