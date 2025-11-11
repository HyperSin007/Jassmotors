<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_view_dashboard()
    {
        $this->actingAs($this->user)
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function dashboard_calculates_total_sales_correctly()
    {
        // Create invoices with items
        $invoice1 = Invoice::create([
            'date' => now(),
            'customer_name' => 'Customer 1',
            'customer_email' => 'customer1@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Street',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'final',
            'total_amount' => 200.00,
            'vat_amount' => 40.00,
            'subtotal' => 160.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'description' => 'Item 1',
            'quantity' => 2,
            'price' => 100.00,
        ]);

        $invoice2 = Invoice::create([
            'date' => now(),
            'customer_name' => 'Customer 2',
            'customer_email' => 'customer2@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Street',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'final',
            'total_amount' => 150.00,
            'vat_amount' => 30.00,
            'subtotal' => 120.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'description' => 'Item 2',
            'quantity' => 3,
            'price' => 50.00,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewHas('totalSales', 350.00);
    }

    /** @test */
    public function dashboard_shows_current_month_sales()
    {
        // Current month invoice
        $currentInvoice = Invoice::create([
            'date' => now(),
            'customer_name' => 'Current Customer',
            'customer_email' => 'current@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Street',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'final',
            'total_amount' => 100.00,
            'vat_amount' => 20.00,
            'subtotal' => 80.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $currentInvoice->id,
            'description' => 'Current Item',
            'quantity' => 1,
            'price' => 100.00,
        ]);

        // Previous month invoice
        $previousInvoice = Invoice::create([
            'date' => now()->subMonth(),
            'customer_name' => 'Previous Customer',
            'customer_email' => 'previous@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Street',
            'invoice_date' => now()->subMonth(),
            'due_date' => now()->subMonth()->addDays(30),
            'status' => 'final',
            'total_amount' => 200.00,
            'vat_amount' => 40.00,
            'subtotal' => 160.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $previousInvoice->id,
            'description' => 'Previous Item',
            'quantity' => 2,
            'price' => 100.00,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewHas('currentMonthSales', 100.00)
            ->assertViewHas('previousMonthSales', 200.00);
    }

    /** @test */
    public function dashboard_only_counts_final_invoices()
    {
        // Draft invoice (should not be counted)
        $draftInvoice = Invoice::create([
            'date' => now(),
            'customer_name' => 'Draft Customer',
            'customer_email' => 'draft@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Street',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 500.00,
            'vat_amount' => 100.00,
            'subtotal' => 400.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $draftInvoice->id,
            'description' => 'Draft Item',
            'quantity' => 5,
            'price' => 100.00,
        ]);

        // Final invoice (should be counted)
        $finalInvoice = Invoice::create([
            'date' => now(),
            'customer_name' => 'Final Customer',
            'customer_email' => 'final@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Street',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'final',
            'total_amount' => 100.00,
            'vat_amount' => 20.00,
            'subtotal' => 80.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $finalInvoice->id,
            'description' => 'Final Item',
            'quantity' => 1,
            'price' => 100.00,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewHas('totalSales', 100.00);
    }

    /** @test */
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }
}
