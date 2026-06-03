<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencija_tretmana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termin_id')->unique()->constrained('termini')->cascadeOnDelete();
            $table->date('datum');
            $table->text('opis_tretmana');
            $table->string('nijansa')->nullable();
            $table->string('proizvodjac')->nullable();
            $table->text('formula')->nullable();
            $table->text('korisceni_preparati')->nullable();
            $table->text('napomena')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencija_tretmana');
    }
};
