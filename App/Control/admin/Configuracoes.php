<?php

use Framework\Control\Page;
use Framework\Database\Transaction;
use Framework\Session\Session;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Configuracoes extends Page
{
    private $replaces;

    public function __construct()
    {
        try {
            $loader = new FilesystemLoader('App/Resources/Templates');
            $twig = new Environment($loader);
            $template = $twig->load('admin/admin_config.html');

            Transaction::open();

            $admin = Pessoa::find(Session::getValue('logged')->getId());
            $cidades = Cidade::all();
            $etapas = VendaStatusOpcoes::all();
            $config = Config::all();

            $this->replaces['cidades'] = $cidades;
            $this->replaces['admin'] = $admin;
            $this->replaces['etapas'] = $etapas;
            $this->replaces['config'] = $config[0];

            parent::add($template->render($this->replaces));
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }

    public function updateAdmin()
    {
        $erros = [];

        if ($_POST) {
            $admin = new Pessoa($_POST['id']);
            $erros['nome'] = $admin->setNome($_POST['nome']);
            $erros['cpf'] = $admin->setCPF($_POST['cpf']);
            $erros['endereco'] = $admin->setEndereco($_POST['endereco']);
            $erros['telefone'] = $admin->setTelefone($_POST['telefone']);
            $erros['email'] = $admin->setEmail($_POST['email']);
            $erros['nascimento'] = $admin->setNascimento($_POST['nascimento']);
            $erros['id_cidade'] = $admin->setId_Cidade($_POST['id_cidade']);
            $admin->setId_Grupo(1);

            if (count($_FILES) > 0 and $_FILES['imagem']['size'] > 0) {
                $admin->setImagem($_FILES['imagem']);
            }
            
            // Verifica se existem erros
            foreach ($erros as $key => $erro) {
                if ($erro != null) {
                    new Message('info', $erro);
                    return;
                }
            }

            try {
                Transaction::open();
                $admin->store();
                Session::setValue('logged', $admin);
                new Message('info', 'Admin atualizado com sucesso.');
                Transaction::close();

                header("Refresh:1; url=?class=Configuracoes", true, 303);
            } catch (Exception $e) {
                Transaction::rollback();
                new Message('error', $e->getMessage());
            }
        }
    }

    public function updateFrete()
    {
        $erros = [];

        if ($_POST) {
            $config = new Config($_POST['id']);
            $erros['valor_frete'] = $config->setValor_Frete($_POST['valor_frete']);
            $erros['valor_minimo_frete'] = $config->setValor_Minimo_Frete($_POST['valor_minimo']);

            // Verifica se existem erros
            foreach ($erros as $key => $erro) {
                if ($erro != null) {
                    new Message('info', $erro);
                    return;
                }
            }

            try {
                Transaction::open();
                $config->store();
                new Message('info', 'Valores atualizados com sucesso.');
                Transaction::close();

                header("Refresh:1; url=?class=Configuracoes", true, 303);
            } catch (Exception $e) {
                Transaction::rollback();
                new Message('error', $e->getMessage());
            }
        }
    }

    public function updateSlider()
    {
        // Pega os nomes das imagens
        $imagensAntigas = LojaIndex::getArquivosSlider();

        if (count($_FILES) > 0 and $_FILES['imagem']['size'] > 0) {
            for ($i = 0; $i < count($_FILES['imagem']['name']); $i++) {

                $img = $_FILES['imagem']['name'][$i];
                $type = $_FILES['imagem']['type'][$i];

                // Verifica o formato da imagem
                if ($type == 'image/jpeg' or $type == 'image/png' or $type == 'image/bmp') {

                    // Move a imagem para o diretório
                    if (move_uploaded_file($_FILES['imagem']['tmp_name'][$i], "App/Resources/Images/Slider/{$img}")) {
                        
                        // Verifica o tamanho da imagem
                        if (ImageHelper::size("App/Resources/Images/Slider/{$img}")) {
                            
                            // Para cada nova imagem upada com sucesso, deleta uma imagem antiga (caso exista).
                            if (isset($imagensAntigas[0])) {
                                unlink($imagensAntigas[0]);
                                unset($imagensAntigas[0]);
                            }

                            new Message('info', 'Upload com tamanho inválido.');
                        } else {
                            unlink("App/Resources/Images/Slider/{$img}");
                            new Message('error', $img . ' com tamanho inválido.');
                        }
                    } else {
                        new Message('error', 'Não foi possivel fazer o upload da imagem. Verifique as permissões de arquivos e pastas.');
                    }
                } else {
                    new Message('error', $img . ' com formato inválido.');
                }
            }
        }
    }
}