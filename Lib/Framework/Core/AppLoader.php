<?php

namespace Framework\Core;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

/**
 * Carrega a classe da aplicação
 */
class AppLoader {
    
    protected $directories;
    
    /**
     * Adiciona um diretório a ser vasculhado
     *
     * @param  string $directory Diretório que contem a Classe a ser vasculhada
     * @return void
     */
    public function addDirectory($directory) {
        $this->directories[] = $directory;
    }
    
    /**
     * Registra o AppLoader
     * @return void
     */
    public function register() {
        spl_autoload_register(array($this, 'loadClass'));
    }
    
    /**
     * Carrega uma classe
     * @param  string $directory Diretório que contem a Classe a ser vasculhada.
     * @return true TRUE caso a classe seja carregada com sucesso. 
     */
    public function loadClass($class) {
        $folders = $this->directories;
        
        foreach ($folders as $folder) {
            if (file_exists("{$folder}/{$class}.php")) {
                require_once "{$folder}/{$class}.php";
                return TRUE;
            } else {
                if (file_exists($folder)) {
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder),
                                                           RecursiveIteratorIterator::SELF_FIRST) as $entry) {
                        if (is_dir($entry)) {
                            if (file_exists("{$entry}/{$class}.php")) {
                                require_once "{$entry}/{$class}.php";
                                return TRUE;
                            }
                        }
                    }
                }
            }
        }
    }
}
