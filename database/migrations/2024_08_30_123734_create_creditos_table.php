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
            $table->integer('cliente')->nullable();
            $table->string('credito')->nullable();
            $table->string('interes')->nullable();
            $table->string('total_credito')->nullable();
            $table->string('cuotas')->nullable();
            $table->string('cuotas_restantes')->nullable();
            $table->string('cuota_real')->nullable();
            $table->string('modalidad')->nullable();
            $table->string('cuotas_valor')->nullable();
            $table->string('lugar_cobro')->nullable();
            $table->string('pagado')->nullable();
            $table->string('pago_restante')->nullable();
            $table->string('saldo_real')->nullable();
            $table->date('inicio')->nullable();
            $table->date('finalizacion')->nullable();
            $table->string('status')->nullable();
            $table->string('dinero_cancelado')->nullable();
            $table->string('dinero_arecibir')->nullable();
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
        Schema::dropIfExists('creditos');
    }
};
