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
        Schema::create('scraped_competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('country_name')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')
                ->onDelete('set null');  // Cascade delete to null

            $table->foreign('site_id')->references('id')->on('sites_search')
                ->onDelete('set null');  // Cascade delete to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first before dropping the table
        Schema::table('scraped_competitions', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['site_id']);
        });

        Schema::dropIfExists('scraped_competitions');
    }
};
