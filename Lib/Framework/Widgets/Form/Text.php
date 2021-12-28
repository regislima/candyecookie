<?php

namespace Framework\Widgets\Form;

use Framework\Traits\AtributosTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Form\Field;
use Framework\Widgets\Form\FormElementInterface;

/**
 * classe que representa um TextArea
 */
class Text extends Field implements FormElementInterface
{
    private $text;

    use AtributosTrait;

    /**
     * Construtor que define o nome e número de linhas da área do texto
     *
     * @param  string $name Atributo name
     * @param  int $rows Número de linhas
     * @return void
     */
    public function __construct($name, $rows = 3)
    {
        $this->setElement(new Element('textarea'));
        $this->setAttribute('name', $name);
        $this->setAttribute('rows', $rows);
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function show() {
        $this->getElement()->add(htmlspecialchars($this->text));

        // Trait
        $this->glueAttributes();

        $this->getElement()->show();
    }
}
