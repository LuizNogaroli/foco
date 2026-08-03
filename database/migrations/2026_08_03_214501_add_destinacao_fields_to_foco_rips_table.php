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
        Schema::table('foco_rips', function (Blueprint $table) {
            $table->string('destinacao_terreno')->nullable();
            $table->decimal('area_terreno_parcial', 12, 2)->nullable();
            $table->string('destinacao_imovel')->nullable();
            $table->decimal('area_imovel_parcial', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foco_rips', function (Blueprint $table) {
            $table->dropColumn(['destinacao_terreno', 'area_terreno_parcial', 'destinacao_imovel', 'area_imovel_parcial']);
        });
    }
};
