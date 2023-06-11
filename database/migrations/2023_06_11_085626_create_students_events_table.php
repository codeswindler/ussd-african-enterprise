<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void {
        Schema::create('students_events', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('surname')->nullable();
            $table->string('whatsapp')->nullable();
            $table->boolean('receive_updates')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void {
        Schema::dropIfExists('students_events');
    }
};
