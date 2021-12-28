<?php

namespace Framework\Widgets\Container;

use Framework\Widgets\Base\Element;

/**
 * Classe que permite criar um painel com bordas. Opcionalmente com título e rodapé.
 */
class Panel Extends Element
{
    private $head;
    private $body;
    private $footer;

    /**
     * Construtor
     *
     * @param  string $panelTitle Título do painel
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('class', 'card');

        $this->head = new Element('h4');
        $this->head->setAttribute('class', 'card-header');
        $this->body = new Element();
        $this->body->setAttribute('class', 'card-body');
        $this->footer = new Element();
        $this->footer->setAttribute('class', 'card-footer');

        $this->add($this->head);
        $this->add($this->body);
        $this->add($this->footer);
    }

    /**
     * Adiciona conteúdo ao cabeçalho do painel.
     *
     * @param  string $content Conteudo do cabeçalho do painel.
     * @return void
     */
    public function addContentHead($panelTitle)
    {
        if ($panelTitle) {
            $this->head->add($panelTitle);
        }
    }

    /**
     * Adiciona conteúdo ao corpo do painel.
     *
     * @param  string $content Conteudo do corpo do painel.
     * @return void
     */
    public function addContentBody($content)
    {
        $this->body->add($content);
    }

    /**
     * Adiciona conteúdo ao corpo do rodapé.
     *
     * @param  string $content Conteudo do corpo do rodapé.
     * @return void
     */
    public function addContentFooter($content)
    {
        $this->footer->add($content);
    }

    public function getHead()
    {
        return $this->head;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getFooter()
    {
        return $this->footer;
    }
}
