<?php

namespace Framework\Widgets\Dialog;

use Framework\Widgets\Base\Element;

/**
 * Classe que representa a exibição de mensagens ao usuário.
 */
class Message {
        
    /**
     * 
     * @param  string $type Tipo de mensagem que vai ser exibido.
     *                      - info
     *                      - error
     * @param  string $message Mensagem que vai ser exibida.
     * @return void
     */
    public function __construct($type, $message) {
        $div = new Element('div');
        $div->setAttribute('class', 'text-center');

        if ($type == 'info') {
            $div->setAttribute('class', 'alert alert-info');
        }

        if ($type == 'error') {
            $div->setAttribute('class', 'alert alert-danger');
        }

        $div->add($message);
        $div->show();
    }
}