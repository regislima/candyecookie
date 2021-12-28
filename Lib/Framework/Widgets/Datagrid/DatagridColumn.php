<?php

namespace Framework\Widgets\Datagrid;

use Framework\Control\Action;

class DatagridColumn {

    private $name;
    private $label;
    private $action;
    private $transformer;

    public function __construct($name, $label) {
        $this->name = $name;
        $this->label = $label;
    }

    public function getAction() {
        if ($this->action) {
            return $this->action->serialize();
        }
    }

    public function setAction(Action $action) {
        $this->action = $action;
    }

    public function getTransformer() {
        return $this->transformer;
    }

    public function setTransformer($transformer) {
        $this->transformer = $transformer;
    }

    public function getName() {
        return $this->name;
    }

    public function getLabel() {
        return $this->label;
    }
}
