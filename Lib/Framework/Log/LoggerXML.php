<?php

namespace Framework\Log;

class LoggerXML extends Logger {
    
    /**
     * Cria o arquivo de log no formato xml
     *
     * @param  String $message Conteúdo do arquivo de log
     * @return void
     */
    public function write($message) {
        date_default_timezone_set('America/Fortaleza');
        $time = date('Y-m-d H:i:s');
        $text = "<log>\n";
        $text .= " <time>$time</time>\n";
        $text .= " <message>$message</message>\n";
        $text .= "</log>\n";
        $handler = fopen($this->filename . '.xml', 'a');
        fwrite($handler, $text);
        fclose($handler);
    }
}