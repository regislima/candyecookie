<?php

namespace Framework\Widgets\Base;

/**
 * Classe que reprenta um elemento html (<body>, <div> ...)
 */
class Element {

    private $tagName;
    private $attributes;
    private $children;
    
    /**
     * Cria um elemento HTML.
     *
     * @param  string $name Tag HTML do objeto
     *                  - p, div, table, aside, etc.
     * @return void
     */
    public function __construct($name = 'div') {
        $this->tagName = $name;
    }
    
    /**
     * Retorna a tag do objeto.
     *
     * @return string tag Tag do objeto.
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * Adiciona um atributo a tag.
     *
     * @param  string $attribute Nome do atributo.
     * @param  mixed $value Valor do atributo.
     * @return void
     */
    public function setAttribute($attribute, $value) {
        $this->attributes[$attribute] = $value;
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
     * Adiciona sublementos ao elemento HTML. Podem ser tags ou textos.
     *
     * @param  mixed $child Conteúdo.
     * @return void
     */
    public function add($child) {
        $this->children[] = $child;
    }
    
    /**
     * Exibe os objetos filhos
     *
     * @return void
     */
    public function show() {
        $this->open();
        echo "\n";

        if ($this->children) {
            foreach ($this->children as $child) {
                if (is_object($child)) {
                    $child->show();
                } else {
                    if (is_string($child) or is_numeric($child)) {
                        echo $child;
                    }
                }
            }

            $this->close();
        }
    }
    
    /**
     * Exibe a tag de abertura e verifica se o mesmo tem propriedades
     *
     * @return void
     */
    private function open() {
        if ($this->tagName) {
            echo "<{$this->tagName}";
            
            if ($this->attributes) {
                foreach ($this->attributes as $name => $value) {
                    if (is_scalar($value)) {
                        echo " {$name}=\"{$value}\"";
                    }

                    if (is_null($value)) {
                        echo " {$name}";
                    }
                }
            }

            echo '>';
        }
    }
    
    /**
     * Fecha a tag.
     *
     * @return void
     */
    private function close() {
        echo "</{$this->tagName}>";
    }

    public function __toString() {
        ob_start();
        $this->show();
        $content = ob_get_clean();
        return $content;
    }
}