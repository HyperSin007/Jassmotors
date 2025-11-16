<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form id="invoiceForm" method="POST" action="{{ route('admin.invoices.store') }}">
                        @csrf
                        
                        <!-- Customer Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Customer Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                                    <input type="date" id="date" name="date" 
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           required>
                                </div>
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                                    <input type="text" id="customer_name" name="customer_name" 
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           required>
                                </div>
                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" id="customer_email" name="customer_email" 
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           required>
                                </div>
                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" id="customer_phone" name="customer_phone" 
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           required>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea id="customer_address" name="customer_address" rows="3"
                                              class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                              required></textarea>
                                </div>
                                <div>
                                    <label for="car_model" class="block text-sm font-medium text-gray-700 mb-1">Car Model</label>
                                    <input type="text" id="car_model" name="car_model" 
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           placeholder="e.g., Toyota Camry 2020">
                                </div>
                                <div>
                                    <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-1">License Plate Number</label>
                                    <input type="text" id="license_plate" name="license_plate" 
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           placeholder="e.g., ABC-123">
                                </div>
                            </div>
                        </div>

                        <!-- Service Items -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Service Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-300">
                                                Item Description
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-300 w-32">
                                                Quantity
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-300 w-32">
                                                Rate (€)
                                            </th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-300 w-32">
                                                Amount
                                            </th>
                                            <th class="px-4 py-3 w-16"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsContainer" class="bg-white divide-y divide-gray-200">
                                        <!-- Items will be added here by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="addItemBtn" 
                                    class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <!-- Totals -->
                        <div class="mb-8">
                            <div class="flex justify-end">
                                <div class="w-full md:w-96 space-y-2">
                                    <div class="flex justify-between py-2 text-sm">
                                        <span class="font-medium text-gray-700">Sub Total:</span>
                                        <span id="subtotal" class="font-semibold text-gray-900">€0.00</span>
                                    </div>
                                    <div class="flex justify-between py-2 text-sm border-t border-gray-200">
                                        <span class="font-medium text-gray-700">VAT (25.5%):</span>
                                        <span id="vatAmount" class="font-semibold text-gray-900">€0.00</span>
                                    </div>
                                    <div class="flex justify-between py-3 text-base border-t-2 border-gray-300">
                                        <span class="font-bold text-gray-900">Total (EUR):</span>
                                        <span id="grandTotal" class="font-bold text-gray-900 text-lg">€0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4">
                            <button type="button" id="saveDraft"
                                    class="inline-flex items-center px-6 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Save as Draft
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Create Invoice
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const VAT_RATE = 0.255;
        let itemsContainer;

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing...');
            
            itemsContainer = document.getElementById('itemsContainer');
            
            // Set today's date
            const today = new Date();
            const dateStr = today.getFullYear() + '-' + 
                          String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                          String(today.getDate()).padStart(2, '0');
            document.getElementById('date').value = dateStr;
            
            // Add first row
            addItemRow();
            console.log('First row added');

            // Add item button
            document.getElementById('addItemBtn').addEventListener('click', function() {
                console.log('Add button clicked');
                addItemRow();
            });

            // Form submission
            document.getElementById('invoiceForm').addEventListener('submit', function(e) {
                if (!validateItems()) {
                    e.preventDefault();
                }
            });

            // Save as draft
            document.getElementById('saveDraft').addEventListener('click', function(e) {
                e.preventDefault();
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'is_draft';
                inp.value = '1';
                document.getElementById('invoiceForm').appendChild(inp);
                if (validateItems()) {
                    document.getElementById('invoiceForm').submit();
                }
            });
        });

        function addItemRow() {
            const row = document.createElement('tr');
            const id = Date.now();
            
            row.innerHTML = `
                <td class="px-4 py-3 border-r border-gray-300">
                    <input type="text" 
                           name="items[${id}][service_name]" 
                           class="item-desc w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" 
                           placeholder="Enter item description" 
                           required>
                </td>
                <td class="px-4 py-3 border-r border-gray-300">
                    <input type="number" 
                           name="items[${id}][quantity]" 
                           class="item-qty w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm text-center" 
                           min="1" 
                           step="1" 
                           value="1" 
                           required>
                </td>
                <td class="px-4 py-3 border-r border-gray-300">
                    <input type="number" 
                           name="items[${id}][price]" 
                           class="item-price w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm text-center" 
                           min="0" 
                           step="0.01" 
                           placeholder="0.00" 
                           required>
                </td>
                <td class="px-4 py-3 text-right border-r border-gray-300">
                    <span class="item-amount font-semibold text-gray-900">€0.00</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <button type="button" 
                            class="delete-btn text-red-600 hover:text-red-800 font-bold text-xl" 
                            title="Delete item">
                        ×
                    </button>
                </td>
            `;

            // Add event listeners
            row.querySelector('.item-qty').addEventListener('input', updateTotals);
            row.querySelector('.item-price').addEventListener('input', updateTotals);
            row.querySelector('.delete-btn').addEventListener('click', function() {
                if (itemsContainer.children.length > 1) {
                    row.remove();
                    updateTotals();
                } else {
                    alert('You must have at least one item row.');
                }
            });

            itemsContainer.appendChild(row);
            updateTotals();
        }

        function updateTotals() {
            let total = 0;
            
            document.querySelectorAll('#itemsContainer tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const amount = qty * price;
                
                row.querySelector('.item-amount').textContent = '€' + amount.toFixed(2);
                total += amount;
            });

            // VAT is included in the price, so we extract it
            // Total = Subtotal + VAT, where VAT = Subtotal * VAT_RATE
            // Total = Subtotal * (1 + VAT_RATE)
            // Therefore: Subtotal = Total / (1 + VAT_RATE)
            const subtotal = total / (1 + VAT_RATE);
            const vat = total - subtotal;

            document.getElementById('subtotal').textContent = '€' + subtotal.toFixed(2);
            document.getElementById('vatAmount').textContent = '€' + vat.toFixed(2);
            document.getElementById('grandTotal').textContent = '€' + total.toFixed(2);
        }

        function validateItems() {
            const rows = document.querySelectorAll('#itemsContainer tr');
            
            if (rows.length === 0) {
                alert('Please add at least one item.');
                return false;
            }

            let hasValidItem = false;
            rows.forEach(row => {
                const desc = row.querySelector('.item-desc').value.trim();
                const qty = parseFloat(row.querySelector('.item-qty').value);
                const price = parseFloat(row.querySelector('.item-price').value);
                
                if (desc && qty > 0 && price >= 0) {
                    hasValidItem = true;
                }
            });

            if (!hasValidItem) {
                alert('Please fill in at least one complete item.');
                return false;
            }

            return true;
        }
    </script>
</x-app-layout>