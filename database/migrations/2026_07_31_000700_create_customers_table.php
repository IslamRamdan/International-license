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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // ربط الجدول بالمستخدم (Foreign Key)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('full_name');
            $table->date('birth_date');
            $table->string('blood_type');
            $table->string('passport_number');
            $table->string('status')->default('pending');
            $table->integer('license_duration');
            // مسارات الصور المرفوعة
            $table->string('personal_photo');
            $table->string('local_license');
            $table->string('passport_photo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
