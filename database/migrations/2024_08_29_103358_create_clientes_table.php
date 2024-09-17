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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('dni')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('localidad')->nullable();
            $table->string('comercio_address')->nullable();
            $table->string('comercio_localidad')->nullable();
            $table->string('comercio_tipo')->nullable();
            $table->string('recorrido')->nullable();
            $table->string('vendedor')->nullable();
            $table->string('prenda')->nullable();
            $table->string('status')->nullable();
            $table->boolean('active')->default(true);
            $table->string('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
