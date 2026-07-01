<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->boolean('priority')->default(false)->after('completed');
            $table->string('status')->default('pending')->after('priority');
            $table->dateTime('due_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn(['priority', 'status', 'due_at']);
        });
    }
};