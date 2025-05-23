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
        // 'name', 'pdf_pages','pdf_limit','price'
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // pl. 'Free', 'Pro', 'Enterprise'
            $table->integer('pdf_limit')->default(3); // Vagy amit akarsz
            $table->integer('pdf_pages');
            $table->integer('price')->nullable();
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
