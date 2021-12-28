<?php

namespace Framework\Widgets\Form;

use Framework\Traits\AtributosTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Form\Field;
use Framework\Widgets\Form\FormElementInterface;

/**
 * Classe que representa um label.
 */
class Label extends Field implements FormElementInterface
{
    use AtributosTrait;

    /**
     * Cria um label e define seu rótulo.
     *
     * @param  string $value Valor do rótulo.
     * @return void
     */
    public function __construct($value)
    {
        $this->setElement(new Element('label'));
        $this->getElement()->add($value);
    }

    public function show()
    {
        // Trait
        $this->glueAttributes();

        $this->getElement()->show();
    }
}
