<?php

namespace Framework\Log;

/**
 * Classe concreta que cria logs no formato html.
 */
class LoggerHTML extends Logger {
    
    /**
     * Cria o arquivo de log no formato txt
     *
     * @param  String $message Conteúdo do arquivo de log
     * @return void
     */
    public function write($message) {
        date_default_timezone_set('America/Fortaleza');
        $time = date('Y-m-d H:i:s');
        
        // monta a string
        $text = "<p>\n";
        $text.= "   <b>$time</b> : \n";
        $text.= "   <i>$message</i> <br>\n";
        $text.= "</p>\n";
        
        // adiciona ao final do arquivo
        $handler = fopen($this->filename, 'a');
        fwrite($handler, $text);
        fclose($handler);
    }
}