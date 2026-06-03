<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klijenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('ime');
            $table->string('prezime');
            $table->string('telefon')->nullable();
            $table->string('adresa')->nullable();
            $table->date('datum_rodjenja')->nullable();
            $table->text('napomena')->nullable();
            $table->text('preferencije')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klijenti');
    }
};
