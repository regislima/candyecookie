<?php

namespace Framework\Widgets\Datagrid;

use Framework\Widgets\Datagrid\DatagridColumn;
use Framework\Control\ActionInterface;

class Datagrid {

    private $columns;
    private $itens;
    private $actions;

    public function addColumn(DatagridColumn $object) {
        $this->columns[] = $object;
    }
    
    /**
     * Adiciona uma ação em uma célula ou botão do datagrid.
     *
     * @param  string $label Rótulo da ação.
     * @param  ActionInterface $action Ação que será realizada ao clicar no botão.
     * @param  string $field
     * @param  string $image Classe css que contem a imagem ou icone do botão.
     * @param  string $data Conteúdo que ficará entre a tags 'i'. Ex: <i>$data</i>
     * @param  array $attribute Adiciona mais atributos ao botão ou link.
     * @return void
     */
    public function addAction($label, ActionInterface $action, $field, $image = null, $data = null, $attribute = null) {
        $this->actions[] = [
            'label' => $label,
            'action' => $action,
            'field' => $field,
            'image' => $image,
            'data' => $data,
            'attribute' => $attribute,
        ];
    }

    public function addItem($object) {
        $this->itens[] = $object;

        foreach ($this->columns as $column) {
            $name = $column->getName();

            if ($object->{'get' . $name}()) {
                $object->{'get' . $name}();
            }
        }
    }

    public function clear() {
        $this->itens = [];
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getItens() {
        return $this->itens;
    }

    public function getActions() {
        return $this->actions;
    }
}
