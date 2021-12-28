<?php

use Framework\Database\Record;

class Pessoa extends Record
{
    const TABLENAME = 'pessoa';
    private $grupo;

    /**
     * Retorna a cidade que a pessoa pertence
     *
     * @return object Objeto cidade
     */
    public function getCidade()
    {
        if (empty($this->cidade)) {
            $cidade = new Cidade($this->getId_Cidade());
        }

        return $cidade;
    }

    /**
     * Retorna a grupo que a pessoa pertence
     *
     * @return object Objeto grupo
     */
    public function getGrupo()
    {
        if (empty($this->grupo)) {
            $this->grupo = new Grupo($this->getId_Grupo());
        }

        return $this->grupo;
    }
 
    public function getNome()
    {
        if (isset($this->data['nome'])) {
            return $this->data['nome'];
        }
    }

    public function setNome($nome)
    {
        $tmp = FieldValidatorHelper::validaNome($nome);

        if ($tmp) {
            return $tmp;
        }

        $this->data['nome'] = $nome;
    }

    public function getCPF()
    {
        if (isset($this->data['cpf'])) {
            return $this->data['cpf'];
        }
    }

    public function setCPF($cpf)
    {
        // Remove os caracteres especiais.
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf);
        $tmp = FieldValidatorHelper::validaCPF($cpf);

        if ($tmp) {
            return $tmp;
        }

        $this->data['cpf'] = $cpf;
    }

    public function getEndereco()
    {
        if (isset($this->data['endereco'])) {
            return $this->data['endereco'];
        }
    }

    public function setEndereco($endereco)
    {
        $tmp = FieldValidatorHelper::validaEndereco($endereco);

        if ($tmp) {
            return $tmp;
        }

        $this->data['endereco'] = $endereco;
    }

    public function getBairro()
    {
        if (isset($this->data['bairro'])) {
            return $this->data['bairro'];
        }
    }

    public function setBairro($bairro)
    {
        $tmp = FieldValidatorHelper::validaBairro($bairro);

        if ($tmp) {
            return $tmp;
        }

        $this->data['bairro'] = $bairro;
    }

    public function getTelefone()
    {
        if (isset($this->data['telefone'])) {
            return $this->data['telefone'];
        }
    }

    public function setTelefone($telefone)
    {
        // Remove os caracteres especiais.
        $telefone = preg_replace( '/[^0-9]/is', '', $telefone);
        $tmp = FieldValidatorHelper::validaTelefone($telefone);

        if ($tmp) {
            return $tmp;
        }

        $this->data['telefone'] = $telefone;
    }

    public function getEmail()
    {
        if (isset($this->data['email'])) {
            return $this->data['email'];
        }
    }

    public function setEmail($email)
    {
        $tmp = FieldValidatorHelper::validaEmail($email);

        if ($tmp) {
            return $tmp;
        }

        $this->data['email'] = $email;
    }

    public function getSenha()
    {
        if (isset($this->data['senha'])) {
            return $this->data['senha'];
        }
    }

    public function setSenha($senha)
    {
        $tmp = FieldValidatorHelper::validaSenha($senha);

        if ($tmp) {
            return $tmp;
        }

        $this->data['senha'] = crypt($senha, '$2a$08$Cf1f11ePArKlBJomM0F6aJ$');
    }

    public function getNascimento()
    {
        if (isset($this->data['nascimento'])) {
            return FieldValidatorHelper::data_para_exibir($this->data['nascimento']);
        }
    }

    public function setNascimento($dataDeNascimento)
    {
        $tmp = FieldValidatorHelper::checkData($dataDeNascimento);

        if ($tmp) {
            return $tmp;
        }

        $this->data['nascimento'] = FieldValidatorHelper::data_para_banco($dataDeNascimento);
    }

    public function getImagem()
    {
        if (isset($this->data['imagem'])) {
            return $this->data['imagem'];
        }
    }

    public function setImagem($imagem)
    {
        $imagemHelper = new ImagemHelper($imagem, $this);

        if ($imagemHelper->checkImagem()) {
            if ($imagemHelper->addImagem()) {
                $this->data['imagem'] = $imagem['name'];
            } else {
                return 'Não foi possivel salvar a imagem na pasta especificada.';
            }
        } else {
            return 'Formato da imagem inválido.';
        }
    }

    public function getId_Grupo()
    {
        if (isset($this->data['id_grupo'])) {
            return $this->data['id_grupo'];
        }
    }

    public function setId_Grupo($idGrupo)
    {
        $tmp = FieldValidatorHelper::validaId($idGrupo);

        if ($tmp) {
            return $tmp;
        }
        $this->data['id_grupo'] = $idGrupo;
    }

    public function getId_Cidade()
    {
        if (isset($this->data['id_cidade'])) {
            return $this->data['id_cidade'];
        }
    }

    public function setId_Cidade($nomeCidade)
    {
        if ($nomeCidade) {
            $objCidade = Cidade::getCidadeByName($nomeCidade);
            $tmp = FieldValidatorHelper::validaId($objCidade->getId());

            if ($tmp) {
                return $tmp;
            }

            $this->data['id_cidade'] = $objCidade->getId();
        } else {
            return 'Campo "Cidade" é obrigatório.';
        }
    }

    public function getCredito()
    {
        if (isset($this->data['credito'])) {
            return $this->data['credito'];
        }
    }

    public function setCredito($credito)
    {
        if ($credito < 0) {
            return 'Valor inválido';
        }

        $this->data['credito'] = $credito;
    }

    public function getData_Cadastro()
    {
        if (isset($this->data['data_cadastro'])) {
            return $this->data['data_cadastro'];
        }
    }

    public function __toString()
    {
        return $this->getNome();
    }
}
