<?php

namespace Framework\Widgets\Form;

use Framework\Control\ActionInterface;
use Framework\Traits\AtributosTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Form\Field;
use Framework\Widgets\Form\FormElementInterface;

/**
 * Classe que representa um button. Usado somente me formulários.
 */
class Button extends Field implements FormElementInterface
{
    private $action;
    private $componentName;

    use AtributosTrait;

    public function __construct($name)
    {
        $this->setElement(new Element('button'));
        $this->setAttribute('name', $name);
    }
    
    /**
     * Adiciona uma ação ao botão.
     *
     * @param  ActionInterface $action
     * @param  mixed $label
     * @return void
     */
    public function setAction(ActionInterface $action, $label)
    {
        $this->action = $action;

        if ($label) {
            $this->setLabel($label);
        }
    }
    
    /**
     * Atribui ao botão o valor do 'name' do componente.
     *
     * @param  string $name Atributo name.
     * @return void
     */
    public function setComponentName($name)
    {
        $this->componentName = $name;
    }

    public function show()
    {
        if ($this->action) {
            $url = $this->action->serialize();
            $this->setAttribute('onclick', "document.{$this->componentName}.action='{$url}'; document.{$this->componentName}.submit();");
        }

        $this->getElement()->add($this->getLabel());

        // Trait
        $this->glueAttributes();

        $this->getElement()->show();
    }
}
