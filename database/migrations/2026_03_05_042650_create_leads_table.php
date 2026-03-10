<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->string('source')->default('manual'); // manual, website, referral, campaign, whatsapp, email
            $table->enum('status', ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'])->default('new');
            $table->string('priority')->default('medium'); // low, medium, high
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->string('currency')->default('IDR');
            $table->text('notes')->nullable();
            $table->string('tags')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('expected_close_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
