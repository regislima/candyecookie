<?php

namespace Framework\Widgets\Form;

interface FormElementInterface {

    public function add($child);
    public function getAttribute($attribute);
    public function setAttribute($attribute, $value);
    public function getLabel();
    public function setLabel($formLabel);
    public function getBrother();
    public function setBrother($brother);
    public function getPositionBrother();
    public function setPositionBrother($position);
    public function getDivClass();
    public function setDivClass($divClass);
    public function show();
}