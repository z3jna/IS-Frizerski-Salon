<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('termini', function (Blueprint $table) {
            $table->id();
            $table->date('datum');
            $table->time('vreme_pocetka');
            $table->time('vreme_zavrsetka');
            $table->string('status')->default('zakazan')->index();
            $table->text('napomena')->nullable();
            $table->foreignId('klijent_id')->constrained('klijenti')->cascadeOnDelete();
            $table->foreignId('zaposleni_id')->constrained('zaposleni')->cascadeOnDelete();
            $table->foreignId('usluga_id')->constrained('usluge')->restrictOnDelete();
            $table->timestamps();

            $table->index(['datum', 'zaposleni_id', 'vreme_pocetka', 'vreme_zavrsetka'], 'termini_raspored_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termini');
    }
};
