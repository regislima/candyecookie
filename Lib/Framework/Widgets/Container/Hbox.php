<?php

namespace Framework\Widgets\Container;

use Framework\Widgets\Base\Element;

/**
 * Classe que representa uma caixa horizontal.
 */
class HBox extends Element
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Adiciona um elemento a caixa
     *
     * @param  mixed $child Elemento a ser adicionado
     * @return Element Retorna a caixa.
     */
    public function add($child)
    {
        $wrapper = new Element('div');
        $wrapper->setAttribute('style', 'display: inline-block;');
        $wrapper->add($child);
        parent::add($wrapper);

        return $wrapper;
    }
}
