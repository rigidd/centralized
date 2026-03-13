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
        Schema::create('client_redirect_aliases', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id');
            $table->string('url')->index();
            $table->string('alias')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_redirect_aliases');
    }
};
