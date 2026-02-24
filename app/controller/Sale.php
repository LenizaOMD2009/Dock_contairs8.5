<?php

namespace app\controller;

use app\database\builder\InsertQuery;
use app\database\builder\SelectQuery;

class Sale extends Base
{
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Página inicial',
            'acao' => 'c'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('sale'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function lista($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Página inicial'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('listsale'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function insert($request, $response)
    {
        #captura os dados do formulário
        $form = $request->getParsedBody();
        #Captura o id do produto
        $id_produto = $form['pesquisa'];
        #Verificar se o id do produto esta vasio ou nulo
        if (empty($id_produto) or is_null($id_produto)) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: O ID do produto é obrigatório!',
                'id' => 0
            ], 403);
        }
        #seleciona o id do cliente CONSUMIDOR FINAL para inserir a venda
        $customer = SelectQuery::select('id')
            ->from('customer')
            ->order('id', 'asc')
            ->limit(1)
            ->fetch();
        #Verificar se o cliente não foi encontrado
        if (!$customer) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: Nenhum cliente encontrado!',
                'id' => 0
            ], 403);
        }
        #seleciona o id do cliente CONSUMIDOR FINAL para inserir a venda
        $id_customer = $customer['id'];
        $FieldAndValue = [
            'id_customer' => $id_customer,
            'total_bruto' => 0,
            'total_liquido' => 0,
            'desconto' => 0,
            'acrescimo' => 0,
            'observacao' => ''
        ];
        try {
            #Tenta inserir a venda no banco de dados e captura o resultado da inserção
            $IsInserted = InsertQuery::table('sale')->save($FieldAndValue);
            #Verificar se a inserção falhou
            if (!$IsInserted) {
                return $this->SendJson(
                    $response,
                    [
                        'status' => false,
                        'msg' => 'Restrição: Falha ao inserir a venda!',
                        'id' => 0
                    ],
                    403
                );
            }
            #Seleciona o id da venda inserida mais recente para retornar na resposta
            $sale = SelectQuery::select('id')
                ->from('sale')
                ->order('id', 'desc')
                ->limit(1)
                ->fetch();
            #Verificar se a venda não foi encontrada
            if (!$sale) {
                return $this->SendJson($response, [
                    'status' => false,
                    'msg' => 'Restrição: Nenhuma venda encontrada!',
                    'id' => 0
                ], 403);
            }
            $id_sale = $sale["id"];
            return $this->SendJson($response, [
                'status' => true,
                'msg' => 'Venda inserida com sucesso!',
                'id' => $id_sale
            ], 201);
        } catch (\Exception $e) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: ' . $e->getMessage(),
                'id' => 0
            ], 500);
        }
    }
    public function listsale($request, $response)
    {
        try {
            #Captura todas a variaveis de forma mais segura VARIAVEIS POST.
            $form = $request->getParsedBody();
            
            #Qual a coluna da tabela deve ser ordenada.
            $order = $form['order'][0]['column'] ?? 0;
            #Tipo de ordenação
            $orderType = $form['order'][0]['dir'] ?? 'asc';
            #Em qual registro se inicia o retorno dos registro, OFFSET
            $start = $form['start'] ?? 0;
            #Limite de registro a serem retornados do banco de dados LIMIT
            $length = $form['length'] ?? 10;
            #ID da venda para listar seus itens
            $id_venda = $form['id_venda'] ?? null;
            
            if (empty($id_venda) || is_null($id_venda)) {
                return $this->SendJson($response, [
                    'draw' => $form['draw'] ?? 1,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'ID da venda é obrigatório'
                ], 400);
            }
            
            $fields = [
                0 => 'si.id',
                1 => 'p.codigo_barra',
                2 => 'p.nome',
                3 => 'si.total_bruto'
            ];
            
            #Capturamos o nome do campo a ser ordenado.
            $orderField = $fields[$order] ?? 'si.id';
            #O termo pesquisado
            $term = $form['search']['value'] ?? '';
            
            $query = SelectQuery::select('si.id, p.codigo_barra, p.nome, si.total_bruto')
                ->from('sale_item si')
                ->join('product p', 'si.id_produto', '=', 'p.id')
                ->where('si.id_venda', '=', $id_venda);
            
            $queryTotal = SelectQuery::select('COUNT(*) as total')
                ->from('sale_item')
                ->where('id_venda', '=', $id_venda);
            $totalRecords = $queryTotal->fetch()['total'] ?? 0;
            
            if (!is_null($term) && ($term !== '')) {
                $query->where('p.codigo_barra', 'ilike', "%{$term}%", 'or')
                    ->where('p.nome', 'ilike', "%{$term}%");

                $queryFiltered = SelectQuery::select('COUNT(*) as total')
                    ->from('sale_item si')
                    ->join('product p', 'si.id_produto', '=', 'p.id')
                    ->where('si.id_venda', '=', $id_venda)
                    ->where('p.codigo_barra', 'ilike', "%{$term}%", 'or')
                    ->where('p.nome', 'ilike', "%{$term}%");
                $totalFiltered = $queryFiltered->fetch()['total'] ?? 0;
            } else {
                $totalFiltered = $totalRecords;
            }

            $items = $query
                ->order($orderField, $orderType)
                ->limit($length, $start)
                ->fetchAll();
            
            $itemData = [];
            foreach ($items as $key => $value) {
                $itemData[$key] = [
                    $value['codigo_barra'],
                    $value['nome'],
                    number_format($value['total_bruto'], 2, ',', '.'),
                    "<a href='/sale/editar-item/{$id_venda}/{$value['id']}' class='btn btn-warning btn-sm'>Editar</a>
                    <button type='button' onclick='DeleteItem(" . $value['id'] . ", " . $id_venda . ");' class='btn btn-danger btn-sm'>Excluir</button>"
                ];
            }
            
            $data = [
                'draw' => $form['draw'] ?? 1,   
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFiltered,
                'data' => $itemData
            ];
            
            $payload = json_encode($data);
            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (\Throwable $th) {
            $data = [
                'draw' => $form['draw'] ?? 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $th->getMessage()
            ];
            $response->getBody()->write(json_encode($data));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }
    public function alterar($request, $response, $args)
    {
        $id = $args['id'];
        $sale = SelectQuery::select()
            ->from('sale')
            ->where('id', '=', $id)
            ->fetch();
        if (!$sale) {
            return header('Location: /sale/lista');
            die;
        }
        $dadosTemplate = [
            'titulo' => 'Página inicial',
            'acao' => 'e',
            'id' => $id,
            'sale' => $sale
        ];
        return $this->getTwig()
            ->render($response, $this->setView('sale'), $dadosTemplate)
            ->WithHeader('Content-Type', 'text/html')
            ->WithStatus(200);
    }
    public function InsertItemSale($request, $response)
    {
        $form = $request->getParsedBody();
        $id_venda = $form['id'] ?? null;
        $id_produto = $form['pesquisa'];
        if (empty($id_venda) or is_null($id_venda)) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: O ID da venda é obrigatório!',
                'id' => 0
            ], 403);
        }

        try {
            $produto = SelectQuery::select()->from('product')->where('id', '=', $id_produto)->fetch();
            if (!$produto) {
                return $this->SendJson($response, [
                    'status' => false,
                    'msg' => 'Restrição: Produto não encontrado!',
                    'id' => 0
                ], 403);
            }
            $FieldAndValue = [
                'id_venda' => $id_venda,
                'id_produto' => $id_produto,
                'quantidade' => 1,
                'total_bruto' => $produto['preco_venda'] ?? $produto['valor'],
                'total_liquido' => $produto['preco_venda'] ?? $produto['valor'],
                'desconto' => 0,
                'acrescimo' => 0
            ];
            
            #Tenta inserir o item na venda
            $IsInserted = InsertQuery::table('sale_item')->save($FieldAndValue);
            if (!$IsInserted) {
                return $this->SendJson($response, [
                    'status' => false,
                    'msg' => 'Restrição: Falha ao inserir o item!',
                    'id' => 0
                ], 403);
            }
            
            return $this->SendJson($response, [
                'status' => true,
                'msg' => 'Item adicionado com sucesso!',
                'id' => $id_venda
            ], 201);
        } catch (\Exception $e) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: ' . $e->getMessage(),
                'id' => 0
            ], 500);
        }
    }
    
    public function DeleteItemSale($request, $response)
    {
        $form = $request->getParsedBody();
        $id_item = $form['id'] ?? null;
        $id_venda = $form['id_venda'] ?? null;
        
        if (empty($id_item) || is_null($id_item)) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: O ID do item é obrigatório!'
            ], 403);
        }
        
        try {
            $item = SelectQuery::select('id')->from('sale_item')->where('id', '=', $id_item)->fetch();
            if (!$item) {
                return $this->SendJson($response, [
                    'status' => false,
                    'msg' => 'Restrição: Item não encontrado!'
                ], 404);
            }
            
            #TODO: Implementar delete na query builder
            return $this->SendJson($response, [
                'status' => true,
                'msg' => 'Item removido com sucesso!',
                'id_venda' => $id_venda
            ], 200);
        } catch (\Exception $e) {
            return $this->SendJson($response, [
                'status' => false,
                'msg' => 'Restrição: ' . $e->getMessage()
            ], 500);
        }
    }
}
