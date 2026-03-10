<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('position')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('lead_id')->nullable(); // FK added later
            $table->string('avatar')->nullable();
            $table->date('birthday')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->string('tags')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
