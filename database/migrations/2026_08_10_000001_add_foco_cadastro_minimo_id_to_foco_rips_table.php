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
            $table->foreignId('foco_cadastro_minimo_id')->nullable()->after('foco_id')
                ->constrained('foco_cadastros_minimos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foco_rips', function (Blueprint $table) {
            $table->dropConstrainedForeignId('foco_cadastro_minimo_id');
        });
    }
};
