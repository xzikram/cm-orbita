<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ubah unique constraint pada document_number agar kompatibel dengan soft deletes.
     *
     * Masalah: MySQL UNIQUE index tidak peduli soft delete. Jika record di-soft delete,
     * document_number-nya masih memblokir insert baru dengan nomor yang sama.
     *
     * Solusi: Ganti UNIQUE(document_number) menjadi UNIQUE(document_number, deleted_at).
     * Karena MySQL menganggap setiap NULL sebagai nilai unik yang berbeda,
     * maka hanya bisa ada SATU record aktif (deleted_at IS NULL) per document_number,
     * sementara record yang di-soft delete (deleted_at berisi timestamp unik) tidak akan konflik.
     */
    public function up(): void
    {
        Schema::table('processed_documents', function (Blueprint $table) {
            // Hapus unique constraint lama
            $table->dropUnique('processed_documents_document_number_unique');

            // Tambahkan unique constraint baru yang menyertakan deleted_at
            $table->unique(['document_number', 'deleted_at'], 'processed_documents_document_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('processed_documents', function (Blueprint $table) {
            $table->dropUnique('processed_documents_document_number_unique');
            $table->unique('document_number', 'processed_documents_document_number_unique');
        });
    }
};
