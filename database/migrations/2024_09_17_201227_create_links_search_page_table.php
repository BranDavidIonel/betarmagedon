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
            $table->unsignedBigInteger('competition_id')->nullable();  
            $table->unsignedBigInteger('id_site')->nullable(); 
            $table->string('type_game')->nullable();
            $table->string('link_league')->nullable();
            $table->boolean('with_data')->default(false);

            // Define the foreign key constraint
            $table->foreign('competition_id')->references('id')->on('competitions')
                ->onDelete('set null');  // Cascade delete to null if competition is deleted

            $table->foreign('id_site')->references('id')->on('sites_search')
                ->onDelete('set null');  // Cascade delete to null

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first before dropping the table
        Schema::table('links_search_page', function (Blueprint $table) {
            $table->dropForeign(['competition_id']);
            $table->dropForeign(['id_site']);
        });

        Schema::dropIfExists('links_search_page');
    }
};
