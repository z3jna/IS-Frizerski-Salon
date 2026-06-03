<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usluge', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->string('tip_usluge');
            $table->text('opis')->nullable();
            $table->unsignedSmallInteger('trajanje_minuta');
            $table->decimal('cena', 10, 2);
            $table->boolean('dostupnost')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usluge');
    }
};
