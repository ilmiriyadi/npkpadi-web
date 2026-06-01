<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nutrient_deficiencies', function (Blueprint $table) {
            // Menambahkan 3 kolom solusi berdasarkan fase umur
            $table->text('solution_vegetative')->nullable()->after('solution');
            $table->text('solution_generative')->nullable()->after('solution_vegetative');
            $table->text('solution_ripening')->nullable()->after('solution_generative');
        });
    }

    public function down()
    {
        Schema::table('nutrient_deficiencies', function (Blueprint $table) {
            $table->dropColumn(['solution_vegetative', 'solution_generative', 'solution_ripening']);
        });
    }
};