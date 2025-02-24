<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use App\Models\Colecao;
use App\Models\Produto;
use App\Models\ProdutoColecao;

class ColecaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $colecao = Colecao::find($id);
        // produto coleção
        $produto_colecaos = ProdutoColecao::where("id_colecao", $colecao->id)->get();
        // produtos
        $produtos = [];
        foreach($produto_colecaos as $produto_colecao) {
            $produto = Produto::whereId($produto_colecao->id_produto)->get();
            $produto['quantidade_colecao'] = $produto_colecao->quantidade;
            
            array_push($produtos, $produto);
        
        }

        // all products value
        $colecao_value = 0;
        foreach($produtos as $produto) {
            $colecao_value += $produto[0]->valor;
        }

        // collection default value
        if (!isset($colecao['valor'])) {
            $colecao['valor'] = $colecao_value;
        }
        $colecao['produtos'] = $produtos;

        // discount value
        if ($colecao['valor'] < $colecao_value) {
            $invalid_price = $colecao_value;
            $discount_percentage = 100 - number_format($colecao['valor'] * 100 / $invalid_price, 0) . "% OFF";

            return view("site.vercolecao", compact("colecao"))->with(compact("invalid_price", "discount_percentage"));
        }
        return view("site.vercolecao", compact("colecao"));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
    }
}
