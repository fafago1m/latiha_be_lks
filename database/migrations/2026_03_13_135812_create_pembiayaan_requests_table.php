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
        Schema::create('pembiayaan_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('umkm_profile_id')->constrained('umkm_profiles')->onDelete('cascade');
            $table->decimal('nominal_pengajuan', 15, 2);
            $table->integer('tenor_bulan');
            $table->decimal('bunga_persen', 5, 2);
            $table->enum('status_approval', ['pending', 'approved_tier_1', 'approved_tier_2', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembiayaan_requests');
    }
};
