<?php

namespace Framework\Widgets\Wrapper;

use Framework\Widgets\Base\Element;
use Framework\Widgets\Container\Panel;
use Framework\Widgets\Form\Button;
use Framework\Widgets\Form\Form;

/**
 * Classe que renderiza o formulário a partir da classe Form
 */
class FormWrapper
{
    private $decorated;
    private $formWrapper;
    private $panel;

    public function __construct(Form $form)
    {
        $this->formWrapper = new Element('form');
        $this->formWrapper->setAttribute('name', $form->getName());
        $this->formWrapper->setAttribute('enctype', 'multipart/form-data');
        $this->formWrapper->setAttribute('method', 'post');
        $this->decorated = $form;
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->decorated, $method), $parameters);
    }

    public function show()
    {
        foreach ($this->decorated->getFields() as $field) {
            $group = new Element();

            if ($field->getLabel()) {
                $group->add($field->getLabel());
            }

            if ($field->getPositionBrother() == 'before') {
                $group->add($field->getBrother());
                $group->add($field);
            } else {
                if ($field->getPositionBrother() == 'after') {
                    $group->add($field);
                    $group->add($field->getBrother());
                } else {
                    $group->add($field);
                }
            }
            
            $group->setAttribute('class', $field->getDivClass());
            $this->formWrapper->add($group);
        }

        if ($this->decorated->getActions()) {
            foreach ($this->decorated->getActions() as $label => $action) {
                $button = new Button($this->decorated->getName() . '_' . strtolower($label));
                $button->setComponentName($this->decorated->getName());
                $button->setAction($action, $label);
                $button->setAttribute('class', 'btn btn-primary');

                if ($this->panel) {
                    $this->panel->addContentFooter($button);
                } else {
                    $this->formWrapper->add($button);
                }
            }
        }

        if ($this->panel) {
            $this->panel->addContentHead($this->decorated->getTitle());
            $this->panel->addContentBody($this->formWrapper);
            $this->panel->show();
        } else {
            $this->formWrapper->show();
        }
        
    }

    public function getPanel() {
        return $this->panel;
    }

    public function setPanel(Panel $panel)
    {
        $this->panel = $panel;
    }
}
