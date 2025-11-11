<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('View Invoice') }}
                </h2>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.invoices.pdf', $invoice) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Download PDF
                    </a>
                    @if ($invoice->status === 'draft')
                        <form action="{{ route('admin.invoices.finalize', $invoice) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Finalize Invoice
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Invoice Header -->
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-lg font-bold mb-2">Invoice Details</h3>
                                <p><span class="font-semibold">Invoice #:</span> {{ $invoice->id }}</p>
                                <p><span class="font-semibold">Date:</span> {{ $invoice->date->format('M d, Y') }}</p>
                                <p>
                                    <span class="font-semibold">Status:</span>
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $invoice->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold mb-2">Customer Information</h3>
                                <p><span class="font-semibold">Name:</span> {{ $invoice->customer_name }}</p>
                                <p><span class="font-semibold">Email:</span> {{ $invoice->customer_email }}</p>
                                <p><span class="font-semibold">Phone:</span> {{ $invoice->customer_phone }}</p>
                                <p><span class="font-semibold">Address:</span> {{ $invoice->customer_address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($invoice->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->service_name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">&euro;{{ number_format($item->price, 2) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">&euro;{{ number_format($item->discount, 2) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">&euro;{{ number_format(($item->quantity * $item->price) - $item->discount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-900">Subtotal:</td>
                                    <td class="px-6 py-3 text-left text-sm text-gray-900">&euro;{{ number_format($invoice->total_discount, 2) }}</td>
                                    <td class="px-6 py-3 text-left text-sm text-gray-900">&euro;{{ number_format($invoice->total_amount - $invoice->total_discount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>