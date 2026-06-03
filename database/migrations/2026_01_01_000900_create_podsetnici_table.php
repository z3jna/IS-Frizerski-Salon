<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('podsetnici', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klijent_id')->constrained('klijenti')->cascadeOnDelete();
            $table->foreignId('termin_id')->nullable()->constrained('termini')->nullOnDelete();
            $table->dateTime('datum_slanja');
            $table->string('tip_podsetnika');
            $table->text('sadrzaj');
            $table->string('status')->default('planiran')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podsetnici');
    }
};
