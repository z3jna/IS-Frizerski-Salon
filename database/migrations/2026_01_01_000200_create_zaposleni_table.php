<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zaposleni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('ime');
            $table->string('prezime');
            $table->string('telefon')->nullable();
            $table->string('pozicija')->nullable();
            $table->string('radno_vreme')->nullable();
            $table->date('datum_zaposlenja')->nullable();
            $table->decimal('plata', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zaposleni');
    }
};
