<?php

namespace Framework\Widgets\Form;

use Framework\Control\ActionInterface;
use Framework\Widgets\Form\FormElementInterface;
use stdClass;

/**
 * Classe que representa um formulário com seus campos e ações.
 */
class Form
{
    private $title;
    private $name;
    private $fields;
    private $actions;

    public function __construct($name = 'my_form')
    {
        $this->name = $name;
    }

    /**
     * Adiciona um campo com um label ao formulário.
     *
     * @param  string $label Rótulo do campo.
     * @param  FormElementInterface $object Campo que será adicionado (entry, combo, label, etc).
     * @param  FormElementInterface $object com o mesmo nivel hierárquico.
     * @param  string $positionBrother Define se o elemento $brother será posicionado antes ou depois do $object. Valores aceitos são 'before' e 'after'..
     * @param  string $divClass Classe que será atribuida a div que circunda o $object.
     * @return void
     */
    public function addField($label, FormElementInterface $object, $brother, $positionBrother, $divClass = null)
    {
        if ($label) {
            $object->setLabel($label);
        }

        if ($brother) {
            $object->setBrother($brother);
        }

        if ($divClass) {
            $object->setDivClass($divClass);
        }
        
        $object->setPositionBrother($positionBrother);
        $this->fields[$object->getAttribute('name')] = $object;
    }

    /**
     * Adiciona uma ação.
     *
     * @param  string $label Rótulo da ação.
     * @param  ActionInterface $action Ação que será disparada.
     * @return void
     */
    public function addAction($label, ActionInterface $action)
    {
        $this->actions[$label] = $action;
    }

    /**
     * Atribui valores aos campos do formulário.
     *
     * @param  object $object Instancia de um objeto.
     * @return void
     */
    public function setData($object)
    {
        foreach ($this->fields as $name => $field) {
            
            if ($name == 'imagem[]') {
                $name = 'imagem';
            }

            if ($name and $object->{'get' . $name}()) {
                // Verifica se o campo é um textarea
                if ($field instanceof Text) {
                    $field->setText($object->{'get' . $name}());
                } else {
                    $field->setAttribute('value', $object->{'get' . $name}());
                }
            }
        }
    }

    /**
     * Recupera do valores dos campos do formulário. Esse método é usado para inserção e atualização de objetos.
     *
     * @param  mixed $class Classe modelo.
     * @return object Retorna um objeto da classe modelo, caso não seja especificado, retorna stdClass.
     */
    public function getData($class = 'stdClass')
    {
        $object = new $class;

        foreach ($this->fields as $key => $field) {
            $value = isset($_POST[$key]) ? $_POST[$key] : null;

            if ($field->getAttribute('type') == 'file') {
                if (count($_FILES['imagem']) > 0 and $_FILES['imagem']['size'][0] > 0) {

                    // Se upload multiplo
                    if ($key != 'imagem') {
                        $key = 'imagem';
                    }

                    $object->{'set' . $key}($_FILES['imagem']);
                }
            } else {
                $validacao[] = $object->{'set' . $key}($value);
            }
        }

        // Percorre o vetor de erros.
        foreach ($validacao as $value) {

            // Verifica se tem algum erro.
            if ($value) {

                // Preenche o formulário com os valores já informados
                $this->setData($object);

                // Retorna o erro.
                return $value;
            }
        }

        return $object;
    }

    /**
     * Recupera do valores dos campos do formulário. Esse método é usado para formulários de pesquisa.
     *
     * @return object Retorna objeto stdClass.
     */
    public function getDataSeachForm()
    {
        $object = new stdClass;

        foreach ($this->fields as $key => $field) {
            $value = isset($_POST[$key]) ? $_POST[$key] : null;
            $object->$key = $value;

            if (count($_FILES) > 0 and isset($_FILES[$key])) {
                $object->$key = $_FILES[$key];
            }
        }

        return $object;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getActions()
    {
        return $this->actions;
    }
}
