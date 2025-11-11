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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'business_name', 'value' => 'Jass Motors', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'business_address', 'value' => '123 Auto Street, Mechanic Lane', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'business_city', 'value' => 'City, State - 123456', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'business_phone', 'value' => '(123) 456-7890', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'business_email', 'value' => 'info@jassmotors.com', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'invoice_footer', 'value' => 'Thank you for your business!', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'invoice_footer_note', 'value' => 'Please include the invoice number when making payment.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'invoice_footer_contact', 'value' => 'For any questions regarding this invoice, please contact us.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
