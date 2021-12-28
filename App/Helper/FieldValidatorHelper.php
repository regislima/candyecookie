<?php

final class FieldValidatorHelper
{
    /**
     * Verifica se o nome informado é válido.
     *
     * @param  string $nome.
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function validaNome($nome)
    {
        $erro = null;

        // Verifica se foi informado alguma coisa
        if ($nome) {

            // Verifica se contem somente letras e espaços.
            if (preg_match('|^[\pL\s]+$|u', $nome)) {

                // Verifica a quantidade de caracteres
                if (strlen($nome) <= 4 or strlen($nome) >= 100) {
                    $erro = 'Campo "Nome" deve ter entre 4 e 100 caracteres.';
                }
            } else {
                $erro = 'Campo "Nome" deve conter somente letras';
            }
        } else {
            $erro = 'Campo "Nome" é obrigatório.';
        }

        return $erro;
    }
    
    /**
     * Verifica se o CPF informado é válido.
     *
     * @param  int $cpf Número do cpf.
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function validaCPF($cpf)
    {
        $erro = null;

        // Verifica se foi informado alguma coisa
        if ($cpf) {

            // Verifica se contem somente números
            if (ctype_digit($cpf)) {
            
                // Verifica se foi informado todos os digitos corretamente
                if (strlen($cpf) != 11) {
                    $erro = 'Campo "CPF" deve conter 11 números.';
                } else {
                    
                    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
                    if (preg_match('/(\d)\1{10}/', $cpf)) {
                        $erro = 'Campo "CPF" não pode ter todos os números iguais.';
                    } else {
                        // Faz o calculo para validar o CPF
                        for ($t = 9; $t < 11; $t++) {
                            for ($d = 0, $c = 0; $c < $t; $c++) {
                                $d += $cpf[$c] * (($t + 1) - $c);
                            }
                            $d = ((10 * $d) % 11) % 10;
                            if ($cpf[$c] != $d) {
                                $erro = 'CPF inválido.';
                            }
                        }
                    }
                }
            } else {
                $erro = 'Campo "CPF" deve conter somente números.';
            }
        } else {
            $erro = 'Campo "CPF" é obrigatório.';
        }

        return $erro;
    }

    /**
     * Verifica se o endereço informado é válido.
     *
     * @param  string $endereco
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function validaEndereco($endereco)
    {
        $erro = null;

        // Verifica se foi informado alguma coisa
        if ($endereco) {

            // Verifica a quantidade de caracteres.
            if (strlen($endereco) <= 4 or strlen($endereco) > 100) {
                $erro = 'Campo "Endereço" deve ter 4 e 100 caracteres.';
            }
        } else {
            $erro = 'Campo "Endereço" é obrigatório.';
        }

        return $erro;
    }

    /**
     * Verifica se o bairro informado é válido.
     *
     * @param  string $bairro
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function validaBairro($bairro)
    {
        $erro = null;

        // Verifica se foi informado alguma coisa
        if ($bairro) {

            // Verifica se contem somente letras.
            if (preg_match('|^[\pL\s]+$|u', $bairro)) {

                // Verifica a quantidade de caracteres.
                if (strlen($bairro) <= 4 or strlen($bairro) > 100) {
                    $erro = 'Campo "Bairro" deve ter 4 e 100 caracteres.';
                }
            } else {
                $erro = 'Campo "Bairro" deve conter somente letras';
            }
        } else {
            $erro = 'Campo "Bairro" é obrigatório.';
        }

        return $erro;
    }

    /**
     * Verifica se o telefone informado é válido.
     *
     * @param  string $telefone
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function validaTelefone($telefone)
    {
        $erro = null;

        // Verifica se foi informado algum número de telefone
        if ($telefone) {
            
            // Verifica se contem somente números.
            if (ctype_digit($telefone)) {

                // Verifica a quantidade de caracteres.
                if (strlen($telefone) != 11) {
                    $erro = 'Campo "Telefone" deve ter 11 números.';
                }
            } else {
                $erro = 'Campo "Telefone" deve conter somente números';
            }
        }

        return $erro;
    }

    /**
     * Verifica se o email informado é válido.
     *
     * @param  string $email
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function validaEmail($email)
    {
        $erro = null;

        // Verifica se foi informado um email.
        if ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = 'Campo "Email" no fomato incorreto.';
            }
        } else {
            $erro = 'Campo "Email" é obrigatório.';
        }

        return $erro;
    }

    /**
     * Verifica se a senha informado é válido.
     *
     * @param  string $senha
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function validaSenha($senha)
    {
        $erro = null;

        // Verifica se foi informado alguma coisa
        if ($senha) {
            
            // Verifica a quantidade de caracteres.
            if (strlen($senha) < 6) {
                $erro = 'Campo "Senha" deve ter 6 ou mais caracteres.';
            }
        } else {
            $erro = 'Campo "Senha" é obrigatório.';
        }

        return $erro;
    }

    /**
     * Verifica se a data informada é válida.
     *
     * @param  string $data
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function checkData($data)
    {
        $erro = null;

        // Verifica se foi informado alguma coisa
        if ($data) {

            // Verifica se foi informada uma data.
            if ($data) {
                $dados = explode('/', $data);
                
                // Verifica se a data é válida.
                if (checkdate($dados[1], $dados[0], $dados[2])) {
                    // Pega o ano atual.
                    $anoAtual = date('Y');

                    // Pega o ano de nascimento informado.
                    $anoNascimento = $dados[2];

                    // Verifica se a data é muito antiga.
                    if (($anoAtual - $anoNascimento) > 100) {
                        $erro = 'Campo "Data de Nascimento" com valor muito antigo.';
                    }

                } else {
                    $erro = 'Campo "Data de Nascimento" inválido.';
                }
            } else {
                $erro = 'Campo "Data de Nascimento" deve ser informada uma data válida.';
            }
        } else {
            $erro = 'Campo "Data de Nascimento" é obrigatório.';
        }

        return $erro;
    }

    /**
     * Verifica se o id é válido.
     *
     * @param  int $id
     * @return string|null Retorna uma mensagem em caso de erro ou null em caso de sucesso.
     */
    static public function validaId($id)
    {
        $erro = null;

        // Verifica se foi informado um id.
        if ($id) {

            // Verifica se o valor informado é numérico positivo e inteiro.
            if (!ctype_digit($id)) {
                $erro = 'Campo com valor inválido.';
            }
        } else {
            $erro = 'Campos de seleção são obrigatórios.';
        }

        return $erro;
    }

    static public function data_para_banco($data)
    {
        $objeto_data = DateTime::createFromFormat("d/m/Y", $data);
        return $objeto_data->format("Y-m-d");
    }

    static public function data_para_exibir($data) {   
        if (is_object($data)) {
            return $data->format("d/m/Y");
        }
    
        $objeto_data = DateTime::createFromFormat("Y-m-d", $data);
        return $objeto_data->format("d/m/Y");
    }

    static public function moeda_para_banco($moeda)
    {
        // Remove (.) do número
        $moeda = str_replace('.', '', $moeda);

        // Substitui (,) por (.)
        $moeda = str_replace(',', '.', $moeda);
        
        return $moeda;
    }

    static public function banco_para_moeda($moeda)
    {
        return number_format($moeda, 2, ',', '.');
    }
}