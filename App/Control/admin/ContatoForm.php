<?php

use Framework\Control\Page;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Email\Email;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ContatoForm extends Page
{
    private $contato;

    public function onReload()
    {
        Transaction::open();

        if (isset($_GET['id'])) {
            $this->contato = Contato::find($_GET['id']);
        }
        
        $replaces['contato'] = $this->contato;
        
        $loader = new FilesystemLoader('App/Resources/Templates');
        $twig = new Environment($loader);
        $template = $twig->load('admin/admin_contato_detalhes.html');
        parent::add($template->render($replaces));

        Transaction::close();
    }

    public function onSend()
    {
        try {
            Transaction::open();

            if (isset($_POST['id'])) {
                $this->contato = Contato::find($_POST['id']);
            }

            $email = new Email;

            if (is_null($this->contato)) {
                $criteria = new Criteria;
                $criteria->add('email', '=', $_POST['email']);
                $repo = new Repository('Pessoa');
                $pessoa = $repo->load($criteria);

                if ($pessoa) {
                    $email->message($_POST['assunto'], $_POST['mensagem'], $pessoa[0]->getNome(), $_POST['email']);
                } else {
                    $email->message($_POST['assunto'], $_POST['mensagem'], $_POST['email'], $_POST['email']);
                }
                
            } else {
                $email->message($this->contato->getAssunto(), $_POST['mensagem'], $this->contato->getCliente(), $this->contato->getCliente()->getEmail());
            }


            if (count($_FILES) > 0 and $_FILES['anexo']['size'] > 0) {
                $email->attach($_FILES['anexo']['tmp_name'], $_FILES['anexo']['name']);
            }

            $email->send();

            if ($email->error()) {
                new Message('error', $email->error()->getMessage());
            } else {
                if ($this->contato != null) {
                    $this->contato->setLida('S');
                    $this->contato->store();
                }
                new Message('info', 'Email enviado com sucesso.');
                header( "Refresh:1; url=index.php?class=ContatoFormList", true, 303);
            }

            Transaction::close();
        } catch (Exception $e) {
            Transaction::rollback();
            new Message('error', $e->getMessage());
        }
    }
}