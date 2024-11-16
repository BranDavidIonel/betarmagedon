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
        Schema::create('scraped_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_search_page_id')->nullable();
            $table->string('team1_name')->nullable();
            $table->string('team2_name')->nullable();
            $table->json('odds')->nullable()->comment('Store betting odds like {"1": 1.85, "x": 3.5, "2": 4.65}');
            $table->dateTime('start_time')->nullable()->comment('The start time of the match');
            $table->string('type')->nullable();
//            $table->float('reverse_odds')->nullable()->comment('Reverse of odds, calculated as a check for profit');
//            $table->boolean('is_profit')->default(false)->comment('Indicates if reverse odds are less than 1, showing profit potential');
//            $table->json('max_bets')->nullable()->comment('Maximum bets for 1, x, and 2 outcomes');
            $table->timestamps();
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
        Schema::table('scraped_matches', function (Blueprint $table) {
            $table->dropForeign(['link_search_page_id']);
        });
        Schema::dropIfExists('scraped_matches');
    }
};
