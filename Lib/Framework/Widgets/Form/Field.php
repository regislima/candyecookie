<?php

namespace Framework\Widgets\Form;

use Framework\Widgets\Base\Element;
use Framework\Widgets\Form\FormElementInterface;

/**
 * Classe abstrata que representa um campo do formulário.
 */
abstract class Field implements FormElementInterface
{
    private $element;
    private $attributes;
    private $label;
    private $brother;
    private $positionBrother;
    private $divClass;
    
    /**
     * Retorna o tipo de elemento do objeto
     *
     * @return Element
     */
    public function getElement() {
        return $this->element;
    }
    
    /**
     * Define o tipo do objeto
     *
     * @param  Element $element
     * @return void
     */
    public function setElement(Element $element) {
        $this->element = $element;
    }

    /**
     * Retorna todos os atributos do objeto.
     * 
     * @return array Array de atributos.
     */
    public function getAllAttributes()
    {
        return $this->attributes;
    }

    /**
     * Retorna um atributo do objeto.
     *
     * @param  string $attribute Nome do atributo.
     * @return string Valor do atributo.
     */
    public function getAttribute($attribute)
    {
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        }
    }

    /**
     * Adiciona um atributo ao objeto.
     *
     * @param  string $attribute Nome do atributo.
     * @param  mixed $value Valor do atributo.
     * @return void
     */
    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * Adiciona um subelemento ao objeto.
     *
     * @param  mixed $child Subelemento que serpa adicionado.
     * @return void
     */
    public function add($child)
    {
        $this->getElement()->add($child);
    }

    /**
     * Retorna o rótulo do objeto.
     *
     * @return string Rótulo do objeto.
     */
    public function getLabel()
    {
        if (isset($this->label)) {
            return $this->label;
        }
    }

    /**
     * Adiciona um rótulo ao objeto.
     *
     * @param  string $label Rótulo
     * @return void
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getBrother() {
        return $this->brother;
    }

    public function setBrother($brother) {
        $this->brother = $brother;
    }

    public function getPositionBrother() {
        return $this->positionBrother;
    }

    public function setPositionBrother($positionBrother) {
        $this->positionBrother = $positionBrother;
    }

    public function getDivClass() {
        return $this->divClass;
    }

    public function setDivClass($divClass) {
        $this->divClass = $divClass;
    }
}
