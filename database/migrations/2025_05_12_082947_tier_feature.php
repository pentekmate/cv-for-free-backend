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
         Schema::create('tier_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained('tiers')->onDelete('cascade');
            $table->string('label');             // megjelenítendő szöveg, pl. "Önéletrajzaid mentése"
            $table->string('value')->nullable(); // ha van konkrét érték (pl. "3/0", "5")
            $table->boolean('is_checked')->default(false); // true = pipálva
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
