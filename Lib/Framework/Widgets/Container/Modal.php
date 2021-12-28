<?php

namespace Framework\Widgets\Container;

use Framework\Widgets\Base\Element;

/**
 * Classe que representa elemento modal.
 */
class Modal extends Element
{

    private $content;
    
    /**
     * Construtor
     *
     * @param  string $id Atributo id do modal.
     * @return void
     */
    public function __construct($id)
    {
        parent::__construct();
        $this->setAttribute('class', 'modal fade');
        $this->setAttribute('id', $id);
        $this->setAttribute('tabindex', '-1');
        $this->setAttribute('role', 'dialog');

        $dialog = new Element();
        $dialog->setAttribute('class', 'modal-dialog');
        $dialog->setAttribute('role', 'document');

        $this->content = new Element();
        $this->content->setAttribute('class', 'modal-content');

        $dialog->add($this->content);
        parent::add($dialog);
    }
    
    /**
     * Adiciona conteúdo ao modal
     *
     * @param  mixed $content Conteúdo do modal.
     * @return void
     */
    public function addContent($content)
    {
        $this->content->add($content);
    }
}