<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_view_invoices_list()
    {
        $this->actingAs($this->user)
            ->get(route('admin.invoices.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.invoices.index');
    }

    /** @test */
    public function user_can_view_create_invoice_page()
    {
        $this->actingAs($this->user)
            ->get(route('admin.invoices.create'))
            ->assertStatus(200)
            ->assertViewIs('admin.invoices.create');
    }

    /** @test */
    public function user_can_create_invoice_with_items()
    {
        $invoiceData = [
            'date' => now()->format('Y-m-d'),
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Test Street',
            'items' => [
                [
                    'service_name' => 'Service Item 1',
                    'quantity' => '2',
                    'price' => '100.00',
                ],
                [
                    'service_name' => 'Service Item 2',
                    'quantity' => '1',
                    'price' => '50.00',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.invoices.store'), $invoiceData);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('invoices', [
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
        ]);

        $invoice = Invoice::where('customer_email', 'test@example.com')->first();
        $this->assertCount(2, $invoice->items);
    }

    /** @test */
    public function invoice_calculates_total_correctly()
    {
        $invoice = Invoice::create([
            'date' => now(),
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Test Street',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 0,
            'vat_amount' => 0,
            'subtotal' => 0,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_name' => 'Item 1',
            'description' => 'Item 1',
            'quantity' => 2,
            'price' => 100.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_name' => 'Item 2',
            'description' => 'Item 2',
            'quantity' => 1,
            'price' => 50.00,
        ]);

        $totalAmount = $invoice->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $this->assertEquals(250.00, $totalAmount);
        
        // VAT calculation (25.5% included in price)
        $subtotal = $totalAmount / 1.255;
        $vatAmount = $totalAmount - $subtotal;
        
        $this->assertEqualsWithDelta(199.20, $subtotal, 0.01);
        $this->assertEqualsWithDelta(50.80, $vatAmount, 0.01);
    }

    /** @test */
    public function user_can_view_single_invoice()
    {
        $invoice = Invoice::create([
            'date' => now(),
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Test Street',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'total_amount' => 250.00,
            'vat_amount' => 50.80,
            'subtotal' => 199.20,
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.invoices.show', $invoice->id))
            ->assertStatus(200)
            ->assertViewIs('admin.invoices.show')
            ->assertSee('Test Customer');
    }

    /** @test */
    public function user_can_generate_pdf()
    {
        $invoice = Invoice::create([
            'date' => now(),
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => '123 Test Street',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'final',
            'total_amount' => 250.00,
            'vat_amount' => 50.80,
            'subtotal' => 199.20,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_name' => 'Service Item',
            'description' => 'Service Item',
            'quantity' => 2,
            'price' => 125.00,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.invoices.pdf', $invoice->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function invoice_validation_requires_customer_name()
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.invoices.store'), [
                'customer_email' => 'test@example.com',
            ]);

        $response->assertSessionHasErrors('customer_name');
    }

    /** @test */
    public function invoice_validation_requires_valid_email()
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.invoices.store'), [
                'customer_name' => 'Test Customer',
                'customer_email' => 'invalid-email',
            ]);

        $response->assertSessionHasErrors('customer_email');
    }

    /** @test */
    public function invoice_requires_at_least_one_item()
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.invoices.store'), [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_phone' => '1234567890',
                'customer_address' => '123 Test Street',
                'invoice_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(30)->format('Y-m-d'),
                'status' => 'draft',
                'items' => [],
            ]);

        $response->assertSessionHasErrors('items');
    }

    /** @test */
    public function guest_cannot_access_invoices()
    {
        $response = $this->get(route('admin.invoices.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.invoices.create'));
        $response->assertRedirect(route('login'));
    }
}
