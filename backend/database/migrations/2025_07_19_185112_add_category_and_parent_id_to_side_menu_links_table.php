<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('side_menu_links', function (Blueprint $table) {
            $table->string('category')->default('side');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('side_menu_links')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('side_menu_links', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['category', 'parent_id']);
        });
    }
};
