<?php

use Framework\Control\Page;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Widgets\Dialog\Message;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Dashboard extends Page
{
    public function __construct()
    {
        try {
            Transaction::open();

            // Vendas no mês
            // Recupera o primeiro e ultimo dia do mes atual
            // $dataInicio = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
            // $dataFim = date('Y-m-d', mktime(23, 59, 59, date('m'), date("t"), date('Y')));
            // $repo = new Repository('stdClass');
            // $valor_total = $repo->directSQL("select sum(valor_final) as total from venda where data_venda between '{$dataInicio}' and '{$dataFim}'");
            // $replaces['total_venda_mes'] = $valor_total[0]->total;

            // Lucro do ano
            $repo = new Repository('stdClass');
            $totalVendaAno = $repo->directSQL("select sum(valor_final) as valor from venda where date_format(data_venda, '%Y') = date('Y')");
            $totalDespesaAno = $repo->directSQL("select sum(valor) as valor from despesa where date_format(data_despesa, '%Y') = date('Y')");
            $replaces['lucro_ano'] = $totalVendaAno[0]->valor - $totalDespesaAno[0]->valor;

            // Clientes cadastrados
            $repo = new Repository('stdClass');
            $replaces['total_clientes'] = $repo->directSQL('select count(*) as total from pessoa')[0]->total;

            // Produtos Cadastrados
            $repo = new Repository('stdClass');
            $replaces['total_produtos'] = $repo->directSQL('select count(*) as total from produto')[0]->total;

            // Contas a receber
            $repo = new Repository('stdClass');
            $replaces['total_receber'] = $repo->directSQL('select sum(valor) as total from conta')[0]->total;

            // Busca os top 5 produtos mais vendidos
            $repo = new Repository('stdClass');
            $arrayItens = $repo->directSQL('select id_produto, sum(quantidade) as total from item_venda group by id_produto order by total desc limit 5');

            foreach ($arrayItens as $value) {
                $produto = new Produto($value->id_produto);
                $value->nome = $produto->getNome();
            }
            
            $replaces['top_produtos'] = $arrayItens;

            // Busca produtos com pouco estoque
            $criteria = new Criteria;
            $criteria->add('estoque', '<=', 10);
            $repo = new Repository('Produto');
            $replaces['estoque'] = $repo->load($criteria);

            // Busca os maiores devedores
            $repo = new Repository('stdClass');
            $arrayItens = $repo->directSQL('select id_cliente, sum(valor) as total from conta where paga = "N" group by id_cliente order by total desc limit 5');

            foreach ($arrayItens as $value) {
                $pessoa = new Pessoa($value->id_cliente);
                $value->nome = $pessoa->getNome();
            }

            $replaces['devedores'] = $arrayItens;

            // Busca as contas com data vencida
            $criteria = new Criteria;
            $criteria->add('dt_vencimento', '<', date('Y-m-d'));
            $criteria->add('paga', '=', 'N');
            $repo = new Repository('Conta');
            $replaces['contas_vencidas'] = $repo->load($criteria);

            // Vendas por mês
            $vendas = Venda::getVendasMes();
            $replaces['labels'] = json_encode(array_keys($vendas));
            $replaces['data_vendas'] = json_encode(array_values($vendas));

            // Despesas por mês
            $despesas = Despesa::getDespesaMes();
            $replaces['labels_despesas'] = json_encode(array_keys($despesas));
            $replaces['data_despesas'] = json_encode(array_values($despesas));

            // Lucro por Mês
            foreach ($vendas as $key => $value) {
                $lucro_mes[$key] = $vendas[$key] - $despesas[$key];
            }

            $replaces['lucro_por_mes'] = json_encode(array_values($lucro_mes));

            $replaces['ano'] = date('Y');

            // Twig
            $loader = new FilesystemLoader('App/Resources/Templates');
            $twig = new Environment($loader);
            $template = $twig->load('admin/admin_dashboard.html');

            parent::add($template->render($replaces));

            Transaction::close();
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }
}