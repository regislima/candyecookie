<?php

namespace Framework\Traits;

/**
 * Adiciona atributos ao objeto Element.
 */
trait AtributosTrait
{
    private function glueAttributes() {
        if ($this->getAllAttributes()) {
            foreach ($this->getAllAttributes() as $attribute => $value) {
                $this->getElement()->setAttribute($attribute, $value);
            }
        }
    }
}