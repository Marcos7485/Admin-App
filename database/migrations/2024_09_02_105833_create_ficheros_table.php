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
            $table->integer('cliente')->nullable();
            $table->date('inicio')->nullable();
            $table->string('cuotas')->nullable();
            $table->string('cuotas_valor')->nullable();
            $table->string('valor_otorgado')->nullable();
            $table->string('valor_final')->nullable();
            $table->string('modalidad')->nullable();
            $table->string('lugar_cobro')->nullable();
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
        Schema::dropIfExists('ficheros');
    }
};
