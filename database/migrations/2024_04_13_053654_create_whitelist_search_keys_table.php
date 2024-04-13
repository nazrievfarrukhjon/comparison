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
        Schema::create('whitelist_search_keys', function (Blueprint $table) {
            $table->id();
            $table->string('names_combo');
            $table->unsignedBigInteger('whitelist_id');

            $table->foreign('whitelist_id')
                ->references('id')
                ->on('whitelists')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whitelist_search_keys');
    }
};
