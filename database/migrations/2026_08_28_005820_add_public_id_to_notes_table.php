<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Note;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            if (!Schema::hasColumn('notes', 'public_id')) {
                $table->uuid('public_id')->nullable()->unique()->after('id');
            }
        });

        // Gera um link único para as notas que já existem no banco
        foreach(Note::all() as $note) {
            if (empty($note->public_id)) {
                $note->public_id = (string) Str::uuid();
                $note->save();
            }
        }
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
};
