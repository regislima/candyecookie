<?php

use Framework\Database\Transaction;

/**
 * Classe que trata o upload de imagens.
 */
class ImagemHelper
{
    private $imagem;
    private $object;

    public function __construct($imagem, $object)
    {
        $this->imagem = $imagem;
        $this->object = $object;
    }

    /**
     * Verifica se o imagem obedece aos tipos de arquivos aceitos.
     *
     * @param  mixed $imagem
     * @return void
     */
    public function checkImagem() {
        $padrao = '/^.+(\.jpg|\.jpeg|\.png)$/';
        $resultado = preg_match($padrao, $this->imagem['name']);
    
        if ($resultado == 0) {
            return false;
        }
    
        return true;
    }
    
    /**
     * Move o imagem para para um local
     *
     * @param  mixed $imagem
     * @return bool
     */
    public function addImagem()
    {
        // Remove imagem antiga do objeto
        $obj = new Pessoa($this->object->getId());
        
        if ($obj->getImagem()) {
            ImagemHelper::remove($obj->getImagem());
        }

        return move_uploaded_file($this->imagem['tmp_name'], "App/Resources/Images/{$this->imagem['name']}");        
    }

    static public function remove($imagem)
    {
        if (is_file("App/Resources/Images/" . $imagem)) {
            return unlink("App/Resources/Images/" . $imagem);
        }
    }
}