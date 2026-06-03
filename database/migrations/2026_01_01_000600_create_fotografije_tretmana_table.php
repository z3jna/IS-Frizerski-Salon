<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotografije_tretmana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidencija_tretmana_id')->constrained('evidencija_tretmana')->cascadeOnDelete();
            $table->string('naziv');
            $table->string('putanja');
            $table->string('tip_fotografije');
            $table->timestamp('datum_dodavanja');
            $table->text('opis')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotografije_tretmana');
    }
};
