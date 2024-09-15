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
        Schema::create('links_search_page', function (Blueprint $table) {
            $table->id();
            $table->string('type_game')->nullable();
            $table->string('link_league')->nullable();
            $table->boolean('with_data')->default(false);
            $table->string('competion_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links_search_page');
    }
};
