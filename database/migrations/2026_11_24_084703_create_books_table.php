<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
        $table->string('penulis', 100);
        $table->string('penerbit', 100);
        $table->unsignedSmallInteger('tahun_terbit');
        $table->unsignedInteger('stok')->default(0);
        $table->string('cover')->nullable();
        $table->timestamps();
    });
}
};
