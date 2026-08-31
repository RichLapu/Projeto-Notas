<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{



    public function up()
{
    Schema::table('notes', function (Blueprint $table) {
        // Adiciona a coluna de proteção (falso por padrão)
        $table->boolean('is_protected')->default(false)->after('text');
    });
}

public function down()
{
    Schema::table('notes', function (Blueprint $table) {
        $table->dropColumn('is_protected');
    });
}



};
