<?php

namespace Framework\Widgets\Wrapper;

use Framework\Widgets\Base\Element;
use Framework\Widgets\Container\Panel;
use Framework\Widgets\Datagrid\Datagrid;

class DatagridWrapper
{
    private $divTable;
    private $decorated;
    private $table;
    private $thead;
    private $tbody;
    private $panel;

    public function __construct(Datagrid $datagrid)
    {
        $this->decorated = $datagrid;
        
        //Criando a tabela
        $this->divTable = new Element();
        $this->divTable->setAttribute('class', 'table-responsive');
        $this->table = new Element('table');
        $this->table->setAttribute('class', 'table');
        $this->thead = new Element('thead');
        $this->thead->setAttribute('class', 'text-center');
        $this->tbody = new Element('tbody');
        $this->tbody->setAttribute('class', 'text-center');
        $this->table->add($this->thead);
        $this->table->add($this->tbody);
        $this->divTable->add($this->table);
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->decorated, $method), $parameters);
    }

    public function __set($attribute, $value)
    {
        $this->decorated->$attribute = $value;
    }

    public function show()
    {
        $this->createHeaders();
        $itens = $this->decorated->getItens();

        foreach ($itens as $item) {
            $this->createItem($this->tbody, $item);
        }

        $this->panel->addContentHead('Registros Encontrados');
        $this->panel->addContentBody($this->divTable);
        $this->panel->show();
    }

    private function createHeaders()
    {
        $row = new Element('tr');
        $actions = $this->decorated->getActions();
        $columns = $this->decorated->getColumns();

        if ($actions) {
            foreach ($actions as $action) {
                $celula = new Element('th');
                $row->add($celula);
            }
        }

        if ($columns) {
            foreach ($columns as $column) {
                $label = $column->getLabel();

                $celula = new Element('th');
                $celula->add($label);
                $row->add($celula);

                if ($column->getAction()) {
                    $url = $column->getAction();
                    $celula->setAttribute('onclick', "document.location='$url'");
                }
            }
        }

        $this->thead->add($row);
    }

    private function createItem($tbody, $item)
    {
        $row = new Element('tr');
        $actions = $this->decorated->getActions();
        $columns = $this->decorated->getColumns();

        if ($actions) {
            foreach ($actions as $action) {
                $label = $action['label'];
                $url = $action['action']->serialize();
                $field = $action['field'];
                $image = $action['image'];
                $key = $item->{'get' . $field}();
                $data = $action['data'];

                $link = new Element('a');
                $link->setAttribute('href', "{$url}&key={$key}&{$field}={$key}");

                if ($action['attribute']) {
                    foreach ($action['attribute'] as $key => $value) {
                        $link->setAttribute($key, $value);
                    }
                }
                
                if ($image) {
                    $i = new Element('i');
                    $i->setAttribute('class', $image);
                    $i->setAttribute('title', $label);
                    $i->add($data);
                    $link->add($i);
                } else {
                    $link->add($label);
                }

                $element = new Element('td');
                $element->add($link);
                $row->add($element);
            }
        }

        if ($columns) {
            foreach ($columns as $column) {
                $name = $column->getName();
                $function = $column->getTransformer();
                $data = $item->{'get' . $name}();

                if ($function) {
                    $data = call_user_func($function, $data);
                }

                $element = new Element('td');
                $element->add($data);
                $row->add($element);
            }
        }

        $this->tbody->add($row);
    }

    public function getPanel()
    {
        return $this->panel;
    }

    public function setPanel(Panel $panel)
    {
        $this->panel = $panel;
    }
}
