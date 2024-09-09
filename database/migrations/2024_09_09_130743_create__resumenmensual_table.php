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
        Schema::create('resumenmensual', function (Blueprint $table) {
            $table->id();
            $table->string('creditos_otorgados');
            $table->string('creditos_renovados');
            $table->string('creditos_refinanciados');
            $table->string('dinero_renovaciones');
            $table->string('pagos_totales');
            $table->date('data');
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
        Schema::dropIfExists('resumenmensual');
    }
};
