<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->get();
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('admin.invoices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string',
            'customer_phone' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $invoice = Invoice::create([
                'date' => $data['date'],
                'customer_name' => $data['customer_name'],
                'customer_address' => $data['customer_address'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'],
                'status' => $request->input('is_draft', false) ? 'draft' : 'final',
            ]);

            $totalAmount = 0;
            $totalDiscount = 0;

            foreach ($data['items'] as $item) {
                $discount = $item['discount'] ?? 0;
                $itemTotal = ($item['quantity'] * $item['price']) - $discount;
                $totalAmount += ($item['quantity'] * $item['price']);
                $totalDiscount += $discount;

                $invoice->items()->create([
                    'service_name' => $item['service_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $discount,
                ]);
            }

            $invoice->update([
                'total_amount' => $totalAmount,
                'total_discount' => $totalDiscount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->input('is_draft', false) ? 'Invoice saved as draft.' : 'Invoice created successfully.',
                'invoice_id' => $invoice->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error creating invoice.'], 500);
        }
    }

    public function show(Invoice $invoice)
    {
        return view('admin.invoices.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $pdf = PDF::loadView('admin.invoices.pdf', compact('invoice'));
        return $pdf->stream('invoice-' . $invoice->id . '.pdf');
    }

    public function finalize(Invoice $invoice)
    {
        $invoice->update(['status' => 'final']);
        return back()->with('status', 'Invoice finalized successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        try {
            // Delete invoice items first
            $invoice->items()->delete();
            
            // Delete the invoice
            $invoice->delete();
            
            return back()->with('success', 'Invoice deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }
}