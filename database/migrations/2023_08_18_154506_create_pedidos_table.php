<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->float("valor", 8, 2);
            $table->timestamp("data");
            $table->timestamp("data_fim");
            $table->boolean("entrega")->default(false);
            $table->boolean("retirada")->default(false);
            $table->float("taxa_entrega", 8, 2)->nullable();
            $table->float("taxa_montagem", 8, 2)->nullable();

            $table->text("observacao")->nullable();

            $table->longText("session_data")->json()->nullable();
            $table->longText("session_id")->nullable();
            $table->longText("uri_pagamento")->nullable();

            $table->text("gateway")->nullable();
            $table->text("status");
            $table->boolean("pago")->default(false);

            $table->unsignedBigInteger("id_forma_de_pagamento");
            $table->foreign('id_forma_de_pagamento')->references('id')->on('formas_de_pagamento');

            $table->unsignedBigInteger("id_endereco");
            $table->foreign('id_endereco')->references('id')->on('enderecos');

            $table->unsignedBigInteger("id_usuario");
            $table->foreign('id_usuario')->references('id')->on('usuarios');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
