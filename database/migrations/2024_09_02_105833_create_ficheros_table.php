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
        Schema::create('ficheros', function (Blueprint $table) {
            $table->id();
            $table->integer('cliente');
            $table->date('inicio');
            $table->string('cuotas');
            $table->string('cuotas_valor');
            $table->string('valor_otorgado');
            $table->string('valor_final');
            $table->string('modalidad');
            $table->string('lugar_cobro');
            $table->string('status');
            $table->string('dinero_cancelado');
            $table->string('dinero_arecibir');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficheros');
    }
};
