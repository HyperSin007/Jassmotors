<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoice Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Logo Settings Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-300">
                                Branding & Logos
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Login Page Logo -->
                                <div>
                                    <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">
                                        Login Page Logo
                                    </label>
                                    @if(isset($settings['site_logo']) && $settings['site_logo'])
                                        <div class="mb-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" 
                                                 alt="Current Logo" 
                                                 class="h-16 object-contain">
                                            <p class="text-xs text-gray-500 mt-2">Current logo</p>
                                        </div>
                                    @endif
                                    <input type="file" 
                                           id="site_logo" 
                                           name="site_logo" 
                                           accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <p class="mt-1 text-xs text-gray-500">Recommended: PNG, JPG, or SVG. Max 2MB</p>
                                    @error('site_logo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Favicon -->
                                <div>
                                    <label for="site_favicon" class="block text-sm font-medium text-gray-700 mb-1">
                                        Favicon
                                    </label>
                                    @if(isset($settings['site_favicon']) && $settings['site_favicon'])
                                        <div class="mb-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <img src="{{ asset('storage/' . $settings['site_favicon']) }}" 
                                                 alt="Current Favicon" 
                                                 class="h-8 object-contain">
                                            <p class="text-xs text-gray-500 mt-2">Current favicon</p>
                                        </div>
                                    @endif
                                    <input type="file" 
                                           id="site_favicon" 
                                           name="site_favicon" 
                                           accept="image/png,image/x-icon,image/vnd.microsoft.icon"
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <p class="mt-1 text-xs text-gray-500">Recommended: ICO or PNG (16x16 or 32x32). Max 1MB</p>
                                    @error('site_favicon')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Business Information Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-300">
                                Business Information
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Business Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="business_name" 
                                           name="business_name" 
                                           value="{{ old('business_name', $settings['business_name'] ?? '') }}"
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                           required>
                                    @error('business_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="business_email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Business Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           id="business_email" 
                                           name="business_email" 
                                           value="{{ old('business_email', $settings['business_email'] ?? '') }}"
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                           required>
                                    @error('business_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="business_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Business Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="business_phone" 
                                           name="business_phone" 
                                           value="{{ old('business_phone', $settings['business_phone'] ?? '') }}"
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                           required>
                                    @error('business_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="business_address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Business Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="business_address" 
                                           name="business_address" 
                                           value="{{ old('business_address', $settings['business_address'] ?? '') }}"
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                           required>
                                    @error('business_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="business_city" class="block text-sm font-medium text-gray-700 mb-1">
                                        City, State - Postal Code <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="business_city" 
                                           name="business_city" 
                                           value="{{ old('business_city', $settings['business_city'] ?? '') }}"
                                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                           placeholder="e.g., New York, NY - 10001"
                                           required>
                                    @error('business_city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Footer Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-300">
                                Invoice Footer Content
                            </h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="invoice_footer" class="block text-sm font-medium text-gray-700 mb-1">
                                        Main Footer Message <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="invoice_footer" 
                                              name="invoice_footer" 
                                              rows="2"
                                              class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                              required>{{ old('invoice_footer', $settings['invoice_footer'] ?? '') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">This appears as the main message in the invoice footer</p>
                                    @error('invoice_footer')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="invoice_footer_note" class="block text-sm font-medium text-gray-700 mb-1">
                                        Payment Note
                                    </label>
                                    <textarea id="invoice_footer_note" 
                                              name="invoice_footer_note" 
                                              rows="2"
                                              class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('invoice_footer_note', $settings['invoice_footer_note'] ?? '') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Instructions about payment (e.g., "Please include the invoice number when making payment")</p>
                                    @error('invoice_footer_note')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="invoice_footer_contact" class="block text-sm font-medium text-gray-700 mb-1">
                                        Contact Information
                                    </label>
                                    <textarea id="invoice_footer_contact" 
                                              name="invoice_footer_contact" 
                                              rows="2"
                                              class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('invoice_footer_contact', $settings['invoice_footer_contact'] ?? '') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Additional contact information or support details</p>
                                    @error('invoice_footer_contact')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="mb-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">
                                Preview (How it will appear on invoice)
                            </h3>
                            <div class="bg-white p-6 rounded shadow-sm">
                                <div class="mb-6">
                                    <h4 class="text-2xl font-bold text-indigo-600 mb-2" id="preview-name">
                                        {{ $settings['business_name'] ?? 'Business Name' }}
                                    </h4>
                                    <p class="text-sm text-gray-600" id="preview-address">
                                        {{ $settings['business_address'] ?? 'Address' }}<br>
                                        {{ $settings['business_city'] ?? 'City, State - Postal Code' }}<br>
                                        Phone: {{ $settings['business_phone'] ?? 'Phone' }}<br>
                                        Email: {{ $settings['business_email'] ?? 'Email' }}
                                    </p>
                                </div>
                                <hr class="my-4">
                                <div class="text-sm text-gray-600 text-center">
                                    <p class="font-semibold text-indigo-600 mb-1" id="preview-footer">
                                        {{ $settings['invoice_footer'] ?? 'Footer message' }}
                                    </p>
                                    <p id="preview-note">{{ $settings['invoice_footer_note'] ?? 'Payment note' }}</p>
                                    <p id="preview-contact">{{ $settings['invoice_footer_contact'] ?? 'Contact info' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.invoices.index') }}" 
                               class="inline-flex items-center px-6 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Settings
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Live preview updates
        document.addEventListener('DOMContentLoaded', function() {
            const fields = {
                'business_name': 'preview-name',
                'business_address': 'preview-address',
                'invoice_footer': 'preview-footer',
                'invoice_footer_note': 'preview-note',
                'invoice_footer_contact': 'preview-contact'
            };

            Object.keys(fields).forEach(fieldId => {
                const input = document.getElementById(fieldId);
                const preview = document.getElementById(fields[fieldId]);
                
                if (input && preview) {
                    input.addEventListener('input', function() {
                        if (fieldId === 'business_address') {
                            const address = document.getElementById('business_address').value;
                            const city = document.getElementById('business_city').value;
                            const phone = document.getElementById('business_phone').value;
                            const email = document.getElementById('business_email').value;
                            preview.innerHTML = `${address}<br>${city}<br>Phone: ${phone}<br>Email: ${email}`;
                        } else {
                            preview.textContent = this.value || 'Enter text...';
                        }
                    });
                }
            });

            // Update address preview when city, phone, or email changes
            ['business_city', 'business_phone', 'business_email'].forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input) {
                    input.addEventListener('input', function() {
                        const address = document.getElementById('business_address').value;
                        const city = document.getElementById('business_city').value;
                        const phone = document.getElementById('business_phone').value;
                        const email = document.getElementById('business_email').value;
                        document.getElementById('preview-address').innerHTML = 
                            `${address}<br>${city}<br>Phone: ${phone}<br>Email: ${email}`;
                    });
                }
            });
        });
    </script>
</x-app-layout>
