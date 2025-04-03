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
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('cv_type_id')->nullable();

            // Létrehozzuk az idegen kulcs kapcsolatot
            $table->foreign('cv_type_id')
                ->references('id')
                ->on('templates')  // A templates tábla id oszlopára hivatkozunk
                ->onDelete('set null');  // Ha törlik a template-t, a cv_type_id NULL-ra áll

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('tier')->nullable();
            $table->string('userName')->nullable();
            $table->string('image')->nullable();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->string('email')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('jobTitle')->nullable();
            $table->text('introduce')->nullable();
            $table->integer('age')->nullable();
            $table->string('ethnic')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
