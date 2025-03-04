<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use App\Models\Colecao;
use App\Models\Endereco;
use App\Models\Produto;
use App\Models\ProdutoColecao;
use App\Models\Item;
use App\Models\Carrinho;

use App\CarrinhoCompras;

use App\Http\Requests\EditItemRequest;
use App\Http\Requests\DeleteItemRequest;

class CarrinhoController extends Controller
{
    public function lista(Request $request) {

        $items = CarrinhoCompras::getItems();
        $total = CarrinhoCompras::getTotal();

        return view("site.carrinho", compact("items", "total"));
    }

    public function add(Request $request) {

        // assert quantidade >= 1
        if (intval($request->quantidade) < 1) {
            return redirect()->back()->withErrors(["msg" => "A quantidade deve ser escolhida."]);
        }

        // colecao
        if (isset($request->type) && $request->type == "colecao") {

            // CarrinhoCompras::getItem()
            
            $colecao = Colecao::whereId($request->id)->first();

            if ($request->quantidade > $colecao->quantidade ) {
                return redirect()->back()->withErrors(["msg" => "Compra indisponível no momento. Tente novamente mais tarde."]);
            }
            
            $item_dados = [
                "tipo" => "colecao",
                "nome" => $colecao->nome,
                "quantidade" => intval($request->quantidade),
                "id_produto" => $colecao->id,
                "produto" => $colecao
            ];

            // produto coleção
            $produto_colecaos = ProdutoColecao::where("id_colecao", $colecao->id)->get();
            // produtos
            $produtos = [];
            foreach($produto_colecaos as $produto_colecao) {
                $produto = Produto::whereId($produto_colecao->id_produto)->get();
                $produto['quantidade_colecao'] = $produto_colecao->quantidade;
                
                array_push($produtos, $produto);
            }

            // set collection value
            if (!isset($colecao['valor'])) {
                $colecao_valor = 0;
                foreach($produtos as $produto) {
                    $colecao_valor += $produto[0]->valor;
                }
                $item_dados['valor'] = $colecao_valor * 1;
            } else {
                $item_dados['valor'] = $colecao['valor'] * 1;
            }
        // produto
        } else {
            $produto = Produto::find($request->id);

            if ($request->quantidade > $produto->quantidade ) {
                return redirect()->back()->withErrors(["msg" => "Compra indisponível no momento. Tente novamente mais tarde."]);
            }

            $item_dados = [
                "tipo" => "produto",
                "nome" => $produto->nome,
                "valor" => $produto->valor * intval($request->quantidade),
                "quantidade" => intval($request->quantidade),
                "id_produto" => $produto->id,
                "produto" => Produto::find($produto->id)
            ];
        }

        if (auth()->guest()) {
            $item_dados['id'] = Str::random(9);
        }

        // check stock
        // if (($item_dados['produto']['quantidade'] - $item_dados['quantidade']) < 0) {
        //     // erro
        //     return redirect()->back()->withErrors(['erro' => 'Não é possível continuar neste momento (possível motivo: falta de estoque)']);
        // }

        CarrinhoCompras::setItem($item_dados);

        return redirect()->route("site.carrinho")->with("sucesso", "Produto adicionado ao carrinho!");
    }

    public function edit(EditItemRequest $request) {

        // $request = json_decode($request->getContent(), true);
        $data = $request->validated();
        // return $data;

        // return $request['id'];
        if ($data['quantidade'] > 0) {
            $new_item = CarrinhoCompras::updateItem($data['id'], [
                'quantidade' => $data['quantidade'],
                'valor' => $data['quantidade'] * CarrinhoCompras::getItem($data['id'])['produto']['valor']
            ]);
            return response()->json([
                'quantidade' => $new_item['quantidade'],
                'valor' => number_format($new_item['valor'], 2, ",", "."),
                'total' => number_format(CarrinhoCompras::getTotal(), 2, ",", ".")
            ]);
        } else {
            CarrinhoCompras::deleteItem($data['id']);
            return response()->json([
                'quantidade' => 0,
                'valor' => 0,
                'total' => number_format(CarrinhoCompras::getTotal(), 2, ",", ".")
            ]);
        }


    }

    public function delete(DeleteItemRequest $request) {
        $data = $request->validated();

        CarrinhoCompras::deleteItem($data['id']);

        return resopnse()->json([
            'total' => number_format(CarrinhoCompras::getTotal(), 2, ",", "."),
            'quantidade_total' => CarrinhoCompras::getItems()
        ]);
    }

    public function proceedToCheckout(Request $request) {
        $items = CarrinhoCompras::getItems(); // items
        $total = CarrinhoCompras::getTotal(); // total carrinho
        $adicional_montagem = CarrinhoCompras::getAdditional(); // adicional de montagem
        $enderecos = Endereco::where("id_usuario", auth()->user()->id)->get(); // user addresses
        $enderecos_retirada = Endereco::where("id_usuario", 1)->get(); // admin addresses

        return view("site.finalizarcompra", compact("items", "total", "adicional_montagem", "enderecos", "enderecos_retirada"));
    }
}
