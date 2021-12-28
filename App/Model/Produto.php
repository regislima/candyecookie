<?php

use Framework\Database\Criteria;
use Framework\Database\Record;
use Framework\Database\Repository;
use Framework\Database\Transaction;

class Produto extends Record
{
    const TABLENAME = 'produto';
    private $unidade;
    private $tipo;
    private $fabricante;
    private $imagens;

    /**
     * Retorna a unidade de medida do objeto
     *
     * @return Unidade Objeto unidade
     */
    public function getUnidade()
    {
        if (empty($this->unidade)) {
            $this->unidade = new Unidade($this->getId_Unidade());
        }

        return $this->unidade;
    }

    /**
     * Retorna o tipo do objeto
     *
     * @return Tipo Objeto Tipo
     */
    public function getTipo()
    {
        if (empty($this->tipo)) {
            $this->tipo = new Tipo($this->getId_Tipo());
        }

        return $this->tipo;
    }

    /**
     * Retorna o Fabricante do objeto
     *
     * @return Fabricante Objeto Fabricante
     */
    public function getFabricante()
    {
        if (empty($this->fabricante)) {
            $this->fabricante = new Fabricante($this->getId_Fabricante());
        }

        return $this->fabricante;
    }

    public function getMargem_Lucro()
    {
        $lucro = $this->data['preco_venda'] - $this->data['preco_custo'];
        $margem = ($lucro / $this->data['preco_venda']) * 100;
        $int = intval($margem);

        // Verifica se o número é exato
        if (($margem - $int) == 0) {
            return $margem + 0;
        }

        return number_format($margem, 2, ',', '.');
    }

    public function getNome()
    {
        if (isset($this->data['nome'])) {
            return $this->data['nome'];
        }
    }

    public function setNome($nome)
    {
        if ($nome) {
            if (trim(strlen($nome) >= 3) and trim(strlen($nome) < 100)) {
                $this->data['nome'] = $nome;
            } else {
                return 'Campo "Nome" deve ter entre 3 e 100 caracteres.';
            }
        } else {
            return 'Campo "Nome" obrigatório.';
        }
    }

    public function getDescricao()
    {
        if (isset($this->data['descricao'])) {
            return $this->data['descricao'];
        }
    }

    public function setDescricao($descricao)
    {
        if ($descricao) {
            if (trim(strlen($descricao) >= 3) and trim(strlen($descricao) <= 255)) {
                $this->data['descricao'] = $descricao;
            } else {
                return 'Campo "Descrição" deve ter entre 3 e 255 caracteres.';
            }
        } else {
            return 'Campo "Descrição" obrigatório.';
        }
    }

    public function getEstoque()
    {
        if (isset($this->data['estoque'])) {
            return $this->data['estoque'];
        }
    }

    public function setEstoque($estoque)
    {
        if ($estoque >= 0 and $estoque <= 10000) {
            $this->data['estoque'] = $estoque;
        } else {
            return 'Campo "Estoque" deve ficar entre 0 e 10000.';
        }
    }

    public function getPreco_Custo()
    {
        if (isset($this->data['preco_custo'])) {
            return FieldValidatorHelper::banco_para_moeda($this->data['preco_custo']);
        }
    }

    public function setPreco_Custo($precoCusto)
    {
        if ($precoCusto) {
            if ($precoCusto >= 0 and $precoCusto <= 100000) {
                $this->data['preco_custo'] = FieldValidatorHelper::moeda_para_banco($precoCusto);
            } else {
                return 'Campo "Preço de Custo" deve ficar entre 0 e 100000.';
            }
        } else {
            return 'Campo "Preço de Custo" obrigatório.';
        }
    }

    public function getPreco_Venda()
    {
        if (isset($this->data['preco_venda'])) {
            return FieldValidatorHelper::banco_para_moeda($this->data['preco_venda']);
        }
    }

    public function setPreco_Venda($precoVenda)
    {
        if ($precoVenda) {
            if ($precoVenda >= 0 and $precoVenda <= 100000) {
                $this->data['preco_venda'] = FieldValidatorHelper::moeda_para_banco($precoVenda);
            } else {
                return 'Campo "Preço de Venda" deve ficar entre 0 e 100000.';
            }
        } else {
            return 'Campo "Preço de Venda" obrigatório.';
        }
    }

    public function getImagem()
    {
        $criteria = new Criteria;
        $criteria->add('id_produto', '=', $this->getId());
        $repo = new Repository('ProdutoImagem');
        $imagens = $repo->load($criteria);
        return $imagens;
    }

    public function setImagem($imagens)
    {       
        if ($imagens and !empty($imagens['name'][0])) {
            
            for ($i = 0; $i < count($imagens['name']); $i++) {

                if (preg_match('/^.+(\.jpg|\.jpeg|\.png)$/', $imagens['name'][$i])) {
                    $this->imagens[] = $imagens['name'][$i];
                    
                    if (!move_uploaded_file($imagens['tmp_name'][$i], "App/Resources/Images/{$imagens['name'][$i]}")) {
                        return 'Erro ao mover o arquivo ' . $imagens['name'][$i];
                    }
                } else {
                    return $imagens['name'][$i] . ' com formato inválido';
                }
            }
        }
    }

    public function getMedida()
    {
        if (isset($this->data['medida'])) {
            return $this->data['medida'];
        }
    }

    public function setMedida($medida)
    {
        if ($medida) {
            if (strlen($medida) > 9) {
                return 'Medida com muitos caracteres';
            } else {
                $this->data['medida'] = $medida;
            }
        }
    }

    public function getId_Unidade()
    {
        if (isset($this->data['id_unidade'])) {
            return $this->data['id_unidade'];
        }
    }

    public function setId_Unidade($idUnidade)
    {
        $tmp = FieldValidatorHelper::validaId($idUnidade);

        if ($tmp) {
            return $tmp; 
        }
        
        $this->data['id_unidade'] = $idUnidade;
    }

    public function getId_Tipo()
    {
        if (isset($this->data['id_tipo'])) {
            return $this->data['id_tipo'];
        }
    }

    public function setId_Tipo($idTipo)
    {
        $tmp = FieldValidatorHelper::validaId($idTipo);

        if ($tmp) {
            return $tmp; 
        }
        
        $this->data['id_tipo'] = $idTipo;
    }

    public function getId_Fabricante()
    {
        if (isset($this->data['id_fabricante'])) {
            return $this->data['id_fabricante'];
        }
    }

    public function setId_Fabricante($nomeFabricante)
    {
        if ($nomeFabricante) {
            $objFab = Fabricante::getFabricanteByName($nomeFabricante);

            if ($objFab) {
                $tmp = FieldValidatorHelper::validaId($objFab->getId());
            } else {
                $tmp = 'Fabricante não cadastrado.';
            }
            
            if ($tmp) {
                return $tmp;
            }

            $this->data['id_fabricante'] = $objFab->getId();
        } else {
            return 'Campo "Fabricante" é obrigatório.';
        }
    }

    public function getObs()
    {
        if (isset($this->data['obs'])) {
            return $this->data['obs'];
        }
    }

    public function setObs($obs)
    {
        if ($obs) {
            if (trim(strlen($obs) >= 3) and trim(strlen($obs) <= 255)) {
                $this->data['obs'] = $obs;
            } else {
                return 'Campo "Observação" deve ter entre 3 e 255 caracteres.';
            }
        }
    }

    public function store($type = 'update')
    {
        parent::store();
        
        if ($type == 'update') {

            if ($this->imagens) {
                foreach ($this->getImagem() as $value) {
                    $value->delete();
                    ImagemHelper::remove($value->getImagem());
                }
            }
        }

        // Pega a imagem do formulário
        if ($this->imagens) {
            foreach ($this->imagens as $imagem) {
                $prod_img = new ProdutoImagem();
                $prod_img->setId_Produto($this->getId());
                $prod_img->setImagem($imagem);
                $prod_img->store();
            }
        }
    }

    public function __toString()
    {
        return $this->getNome();
    }
}
