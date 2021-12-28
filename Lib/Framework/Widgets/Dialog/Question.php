<?php

namespace Framework\Widgets\Dialog;

use Framework\Control\Action;
use Framework\Widgets\Base\Element;

class Question {
     
    public function __construct($message, Action $action_yes, Action $action_no = null) {
        $div = new Element('div');
        $div->setAttribute('class', 'alert alert-warning clearfix');
        $div->setAttribute('role', 'alert');
        //$div->setAttribute('style', 'padding: 20px; margin-top: 10px;');

        # Converte os nome de métodos em URL's
        $url_yes = $action_yes->serialize();
        
        $link_yes = new Element('a');
        $link_yes->setAttribute('href', $url_yes);
        $link_yes->setAttribute('class', 'btn btn-primary pull-right');
        $link_yes->add('Sim');
        $message .= '&nbsp;' . $link_yes;

        if ($action_no) {
            $url_no = $action_no->serialize();
            
            $link_no = new Element('a');
            $link_no->setAttribute('href', $url_no);
            $link_no->setAttribute('class', 'btn btn-primary pull-right');
            $link_no->add('Não');
            $message .= $link_no;
        }

        $div->add($message);
        $div->show();
    }
}