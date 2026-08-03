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
        Schema::table('foco_cadastros_minimos', function (Blueprint $table) {
            $table->string('latitude')->nullable()->after('observacoes');
            $table->string('longitude')->nullable()->after('latitude');
            $table->string('modo_localizacao')->nullable()->after('longitude');
            $table->string('destinacao_terreno')->nullable()->after('modo_localizacao');
            $table->string('area_terreno_parcial')->nullable()->after('destinacao_terreno');
            $table->string('destinacao_imovel')->nullable()->after('area_terreno_parcial');
            $table->string('area_imovel_parcial')->nullable()->after('destinacao_imovel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foco_cadastros_minimos', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'modo_localizacao',
                'destinacao_terreno',
                'area_terreno_parcial',
                'destinacao_imovel',
                'area_imovel_parcial',
            ]);
        });
    }
};
