<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard(Request $request)
    {
        $vatRate = 0.255;

        // Calculate totals with VAT included in prices
        // For each invoice, we need to sum items: quantity * price (VAT included)
        $allInvoices = Invoice::where('status', 'final')->with('items')->get();
        
        $totalSales = 0;
        foreach ($allInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                $totalSales += $item->quantity * $item->price;
            }
        }

        // Current Month Sales
        $currentMonthInvoices = Invoice::where('status', 'final')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->with('items')
            ->get();
        
        $currentMonthSales = 0;
        foreach ($currentMonthInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                $currentMonthSales += $item->quantity * $item->price;
            }
        }

        // Previous Month Sales
        $previousMonthInvoices = Invoice::where('status', 'final')
            ->whereYear('date', now()->subMonth()->year)
            ->whereMonth('date', now()->subMonth()->month)
            ->with('items')
            ->get();
        
        $previousMonthSales = 0;
        foreach ($previousMonthInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                $previousMonthSales += $item->quantity * $item->price;
            }
        }

        // Invoice counts
        $totalInvoices = Invoice::count();
        $currentMonthInvoicesCount = Invoice::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        return view('admin.dashboard', compact(
            'totalSales',
            'currentMonthSales',
            'previousMonthSales',
            'totalInvoices',
            'currentMonthInvoicesCount'
        ));
    }

    /**
     * Show invoices page.
     */
    public function invoices(Request $request)
    {
        return redirect()->route('admin.invoices.index');
    }
}
