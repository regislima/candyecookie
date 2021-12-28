<?php

namespace Framework\Widgets\Form;

use Framework\Traits\AtributosTrait;
use Framework\Widgets\Base\Element;
use Framework\Widgets\Form\Field;
use Framework\Widgets\Form\FormElementInterface;

/**
 * classe que representa um select
 */
class DataList extends Field implements FormElementInterface
{
    private $dataList;
    private $itens;

    use AtributosTrait;
    
    /**
     * Cria um datalist e define o atributo 'list'.
     *
     * @param  string $list Valor do atributo 'name'.
     * @return void
     */
    public function __construct($list)
    {
        $this->setElement(new Element('input'));
        $this->setAttribute('list', $list);
        $this->setAttribute('name', $list);

        $this->dataList = new Element('datalist');
        $this->dataList->setAttribute('id', $list);

        $this->getElement()->add($this->dataList);
    }
    
    /**
     * Adiciona itens ao datalist.
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
            foreach ($this->itens as $value) {
                $option = new Element('option');
                $option->setAttribute('value', $value);

                $this->dataList->add($option);
            }
        }

        // Trait
        $this->glueAttributes();

        $this->getElement()->show();
    }
}
