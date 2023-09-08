<?php

namespace App\Http\Requests;

use App\Models\Endereco;
use App\Models\Forma_de_pagamento;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{

    private $id_endereco, $id_forma_de_pagamento, $date;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */

    public function rules(): array
    {
        $rules = [
            'forma' => ["required", "regex:/^(entrega)|(retirada)$/"],
            'pagamento' => ["required", "regex:/^(pix)|(cc)|(cd)|(boleto)$/"],
            'obs' => "nullable|min:1|max:255",
        ];
        
        // type
        if (!empty($this->get("forma"))) {
            // shipping
            if ($this->get("forma") == "entrega") {
                // address
                $this->id_endereco = Endereco::whereId($this->get("endereco_entrega"))->where("id_usuario", auth()->user()->id)->get()->first()->id;
                $rules['endereco_entrega'] = ["required", "regex:/^" . $this->id_endereco . "$/"]; // address rule
                // $this->replace(['endereco' => $id_endereco]);

                // date
                if (!empty($this->get("date"))) {
                    $rules['date'] = ["required", "regex:/^" . session()->get("shipping_date") . "$/"]; // date rule
                }
                $this->date = session()->get("shipping_date");

            // withdrawal
            } else if ($this->get("forma") == "retirada") {
                // address
                $this->id_endereco = Endereco::whereId($this->get("endereco_retirada"))->where("id_usuario", 1)->get()->first()->id;
                $rules['endereco_retirada'] = ["required", "regex:/^" . $this->id_endereco . "$/"]; // address rule
                // $this->replace(['endereco' => $id_endereco]);

                // date
                $rules['date'] = ["required", "after_or_equal:" . session()->get("min_withdrawal_date")]; // date rule
                $this->date = $this->get("date");
            }
            // payment
            if (!empty($this->get("pagamento"))) {
                $alias_forma_de_pagamento = Forma_de_pagamento::where("alias", $this->get("pagamento"))->get()->first()->alias;
                $this->id_forma_de_pagamento = Forma_de_pagamento::where("alias", $this->get("pagamento"))->get()->first()->id;
                $rules['pagamento'] = ["regex:/^" . $alias_forma_de_pagamento . "$/"];
                // $this->replace(['pagamento' => $id_forma_de_pagamento]);
            }
        }

        return $rules;
    }
}
