<?php

/**
 * 
 * Classe melhorada para manipulação de imagens. 
 */
class ImageHelper
{

    /**
     * Verifica se é uma imagem válida.
     *          - png
     *          - jpeg/jpg
     *          - bmp
     * 
     * @param string $image Imagem
     * @return bool Return true caso seja uma imagem válida. False caso contrário.
     */
    static public function typeVerify(string $image)
    {
        $type = mime_content_type($image);

        switch ($type) {
            case 'image/jpeg':
                return true;
                break;
            
            case 'image/png':
                return true;
                break;

            case 'image/bmp':
                return true;
                break;
            
            default:
                return false;
        }
    }

    /**
     * Verifica se a imagem está no tamanho correto (900x350).
     * 
     * @param string $image Imagem
     * @return bool True em caso de sucesso. False em caso de falha.
     */
    static public function size(string $image)
    {
        $file = getimagesize($image);

        // Verifica se largura e altura estão no padrão (900x350)
        if ($file[0] == 900 and $file[1] == 350) {
            return true;
        }

        return false;
    }
}