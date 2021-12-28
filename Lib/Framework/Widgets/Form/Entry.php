<?php

namespace Framework\Widgets\Form;

use Framework\Traits\AtributosTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Form\Field;
use Framework\Widgets\Form\FormElementInterface;

/**
 * Classe que representa um input text.
 */
class Entry extends Field implements FormElementInterface {

    use AtributosTrait;
    
    /**
     * Cria o objeto
     *
     * @param  string $name Valor do atributo 'name'.
     * @param  string $type Tipo de input (email, date, file, text, password, hidden, radio, checkbox, etc).
     * @return void
     */
    public function __construct($name, $type = 'text')
    {
        $this->setElement(new Element('input'));
        $this->setAttribute('name', $name);
        $this->setAttribute('type', $type);
    }
    
    public function show()
    {
        // Trait
        $this->glueAttributes();
        
        $this->getElement()->show();
    }

}
