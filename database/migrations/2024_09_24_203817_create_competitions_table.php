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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('country_id')->nullable()->default(null);
            $table->json('alias')->nullable()->comment('This field will store alternative names for the competition, such as Liga 1, liga 1, etc.');
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')
                ->onDelete('set null');  // Cascade delete to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first before dropping the table
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
        });
        Schema::dropIfExists('competitions');
    }
};
