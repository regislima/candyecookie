<?php

namespace Framework\Widgets\Form;

use Framework\Traits\AtributosTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Form\Field;
use Framework\Widgets\Form\FormElementInterface;

/**
 * classe que representa um select
 */
class Combo extends Field implements FormElementInterface
{
    private $itens;

    use AtributosTrait;
    
    /**
     * Cria um select e define o atributo 'name'.
     *
     * @param  string $name Valor do atributo 'name'.
     * @return void
     */
    public function __construct($name)
    {
        $this->setElement(new Element('select'));
        $this->setAttribute('name', $name);

        $option = new Element('option');
        $option->add('Escolha');
        $option->setAttribute('value', '0');

        $this->getElement()->add($option);
    }
    
    /**
     * Adiciona itens ao select.
     *
     * @param  mixed $itens Array de itens.
     * @return void
     */
    public function addItens($itens)
    {
        $this->itens = $itens;
    }

    public function show()
    {
        if ($this->itens) {
            foreach ($this->itens as $chave => $item) {
                $option = new Element('option');
                $option->setAttribute('value', $chave);
                $option->add($item);

                if ($chave == $this->getAttribute('value')) {
                    $option->setAttribute('selected', null);
                }

                $this->getElement()->add($option);
            }
        }

        // Trait
        $this->glueAttributes();

        $this->getElement()->show();
    }
}
