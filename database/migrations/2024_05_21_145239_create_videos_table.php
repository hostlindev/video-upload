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
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id'); // Columna id única y auto incremental
            $table->string('nombre', 30);
            $table->string('apellido', 30);
            $table->string('cedula', 15);
            $table->text('enlace'); // Almacena la ruta del video
            $table->ipAddress('ip'); // Almacena la dirección IP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
