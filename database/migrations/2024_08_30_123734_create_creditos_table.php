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
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->integer('cliente');
            $table->string('credito');
            $table->string('interes');
            $table->string('interes_mora');
            $table->string('total_credito');
            $table->string('cuotas');
            $table->string('cuotas_restantes');
            $table->string('modalidad');
            $table->string('cuotas_valor');
            $table->string('lugar_cobro');
            $table->string('pagado');
            $table->string('pago_restante');
            $table->date('inicio');
            $table->date('finalizacion');
            $table->string('mora');
            $table->string('status');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};
