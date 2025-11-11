<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('customer_name');
            $table->text('customer_address');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};