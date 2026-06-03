<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uplate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('racun_id')->constrained('racuni')->cascadeOnDelete();
            $table->date('datum_uplate');
            $table->decimal('iznos', 10, 2);
            $table->string('status_transakcije')->default('uspesno')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uplate');
    }
};
