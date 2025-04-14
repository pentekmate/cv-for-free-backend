<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('previous_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->constrained()->onDelete('cascade'); // Kapcsolat a CV-hez
            $table->string('employer');
            $table->string('jobTitle');
            $table->date('beginDate')->nullable();
            $table->date('endDate')->nullable();
            $table->text('description')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('previous_jobs');
    }
};
