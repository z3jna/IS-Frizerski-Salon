<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('racuni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termin_id')->unique()->constrained('termini')->cascadeOnDelete();
            $table->date('datum_izdavanja');
            $table->decimal('ukupan_iznos', 10, 2);
            $table->string('nacin_placanja')->nullable();
            $table->string('status_placanja')->default('neplaceno')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('racuni');
    }
};
