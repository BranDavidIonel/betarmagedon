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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_search_page_id')->nullable();
            $table->unsignedBigInteger('team1_id')->nullable();
            $table->unsignedBigInteger('team2_id')->nullable();
            $table->json('odds')->nullable()->comment('Store betting odds like {"1": 1.85, "x": 3.5, "2": 4.65}');
            $table->dateTime('start_time')->nullable()->comment('The start time of the match');
            $table->string('type')->nullable();
            $table->timestamps();

            // Define the foreign key constraint
            $table->foreign('team1_id')->references('id')->on('teams')
                ->onDelete('set null');  // Cascade delete to null if competition is deleted
            $table->foreign('team2_id')->references('id')->on('teams')
                ->onDelete('set null');  // Cascade delete to null if competition is deleted

            $table->foreign('link_search_page_id')->references('id')->on('links_search_page')
                ->onDelete('set null');  // Cascade delete to null if competition is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first before dropping the table
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['team1_id']);
            $table->dropForeign(['team2_id']);
            $table->dropForeign(['link_search_page_id']);
        });
        Schema::dropIfExists('matches');
    }
};
