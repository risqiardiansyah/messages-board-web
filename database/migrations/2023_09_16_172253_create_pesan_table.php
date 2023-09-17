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
        Schema::create('pesan', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code')->unique();
            $table->string('id_user')->references('id')->on('user');
            $table->text('isi');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->default(null)->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan');
    }
};
