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
       Schema::table('cvs', function (Blueprint $table) {
            // nullable() kell, hogy a régi 104 rekordnál ne szálljon el hibával (ott NULL lesz az értékük)
            $table->string('cv_type')->nullable()->after('id');
            $table->string('picked_bg')->nullable()->after('cv_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            $table->dropColumn(['cv_type', 'picked_bg']);
        });
    }
};
