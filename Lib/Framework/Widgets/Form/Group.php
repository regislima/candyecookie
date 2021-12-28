<?php

namespace Framework\Widgets\Form;

use Framework\Traits\AtributosTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Form\FormElementInterface;
use Framework\Widgets\Form\Label;
use Framework\Widgets\Form\Field;


/**
 * Classe que representa um grupo (checkboxes ou radiobuttons).
 */
class Group extends Field implements FormElementInterface
{
    private $itens;
    private $name;
    private $type;
    private $style;

    use AtributosTrait;

    /**
     * Construtor que define os atributos name e type do objeto. O padrão do
     * layout é vertical.
     *
     * @param  string $name Valor do atributo 'name'.
     * @param  string $type Tipo de input (radio ou checkbox).
     * @return void
     */
    public function __construct($name, $type)
    {
        $this->setElement(new Element('div'));
        $this->setAttribute('name', $name);
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Adiciona itens para o radiogroup
     *
     * @param  mixed $itens Array de itens
     * @return void
     */
    public function addItens($itens)
    {
        $this->itens = $itens;
    }
    
    /**
     * Adiciona estilo a cada item do grupo.
     *
     * @param  marray $style
     * @return void
     */
    public function AddStyle($style)
    {
        $this->style = $style;
    }

    public function show()
    {
        if ($this->itens) {
            foreach ($this->itens as $index => $label) {
                $this->setElement(new Element('div'));

                if ($this->type == 'radio') {
                    $component = new Entry("{$this->name}", $this->type);
                }

                if ($this->type == 'checkbox') {
                    $component = new Entry("{$this->name}[]", $this->type);
                }

                $component->setAttribute('value', $index);

                if ($index == $this->getAttribute('value')) {
                    $component->setAttribute('checked', null);
                }
                
                $lbl = new Label($label);
                $lbl->add($component);

                // Adicionando os estilos
                if ($this->style) {
                    $this->getElement()->setAttribute('class', $this->style['div']);
                    $lbl->setAttribute('class', $this->style['label']);
                    $component->setAttribute('class', $this->style['item']);
                    $lbl->add($this->style['span']);
                }
                
                $this->getElement()->add($lbl);

                // Trait
                $this->glueAttributes();

                $this->getElement()->show();
            }
        }
    }
}
