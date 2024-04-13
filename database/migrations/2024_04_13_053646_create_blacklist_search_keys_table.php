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
        Schema::create('blacklist_search_keys', function (Blueprint $table) {
            $table->id();
            $table->string('names_combo');
            $table->unsignedBigInteger('blacklist_id');
            $table->timestamps();

            $table->foreign('blacklist_id')
                ->references('id')
                ->on('blacklists')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklist_search_keys');
    }
};
