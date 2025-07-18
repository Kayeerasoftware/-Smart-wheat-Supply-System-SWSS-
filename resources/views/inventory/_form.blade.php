<form action="{{ route('inventory.store') }}" method="POST" class="space-y-6 max-h-[70vh] overflow-y-auto pr-2" x-data x-ref="inventoryForm" @submit.prevent="submitForm($event)">
    @csrf
    <!-- Hidden field for adjustment_type to satisfy backend validation -->
    <input type="hidden" name="adjustment_type" value="receipt">
    <!-- General error message -->
    <template x-if="errorMessage">
        <div class="mb-4 text-red-500 text-center text-sm" x-text="errorMessage"></div>
    </template>
    <!-- Product and Warehouse Selection -->
    <div class="glass-card p-4 rounded-2xl">
        <h3 class="text-lg font-medium font-space mb-4">Product & Location</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-300 mb-1">Product</label>
                <select name="product_id" id="product_id" required class="form-input w-full rounded-lg px-3 py-2 bg-white text-gray-900 dark:bg-gray-800 dark:text-white">
                    <option value="">Select a product</option>
                    @foreach($products as $product)
                        @if(strtolower($product->name) === 'wheat')
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }} class="bg-white text-gray-900 dark:bg-gray-800 dark:text-white">
                                {{ $product->name }} ({{ $product->sku }})
                            </option>
                        @endif
                    @endforeach
                </select>
                <template x-if="errors['product_id']">
                    <div class="text-red-500 text-xs mt-2" x-text="errors['product_id'][0]"></div>
                </template>
            </div>
            <div>
                <label for="warehouse_id" class="block text-sm font-medium text-gray-300 mb-1">Warehouse</label>
                <select name="warehouse_id" id="warehouse_id" required class="form-input w-full rounded-lg px-3 py-2 bg-white text-gray-900 dark:bg-gray-800 dark:text-white">
                    <option value="">Select a warehouse</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }} class="bg-white text-gray-900 dark:bg-gray-800 dark:text-white">
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
                <template x-if="errors['warehouse_id']">
                    <div class="text-red-500 text-xs mt-2" x-text="errors['warehouse_id'][0]"></div>
                </template>
            </div>
        </div>
    </div>
    <!-- Quantity and Details -->
    <div class="glass-card p-4 rounded-2xl">
        <h3 class="text-lg font-medium font-space mb-4">Quantity & Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-300 mb-1">Quantity</label>
                <input type="number" name="quantity" id="quantity" step="0.01" min="0.01" class="form-input w-full rounded-lg px-3 py-2" placeholder="Enter quantity" value="{{ old('quantity') }}" required>
                <template x-if="errors['quantity']">
                    <div class="text-red-500 text-xs mt-2" x-text="errors['quantity'][0]"></div>
                </template>
            </div>
            <div>
                <label for="reference_number" class="block text-sm font-medium text-gray-300 mb-1">Reference Number</label>
                <input type="text" name="reference_number" id="reference_number" class="form-input w-full rounded-lg px-3 py-2" placeholder="PO, Invoice, etc." value="{{ old('reference_number') }}">
                <template x-if="errors['reference_number']">
                    <div class="text-red-500 text-xs mt-2" x-text="errors['reference_number'][0]"></div>
                </template>
            </div>
            <div>
                <label for="batch_number" class="block text-sm font-medium text-gray-300 mb-1">Batch Number</label>
                <input type="text" name="batch_number" id="batch_number" class="form-input w-full rounded-lg px-3 py-2" placeholder="Batch/Lot number" value="{{ old('batch_number') }}">
                <template x-if="errors['batch_number']">
                    <div class="text-red-500 text-xs mt-2" x-text="errors['batch_number'][0]"></div>
                </template>
            </div>
            <div>
                <label for="expiry_date" class="block text-sm font-medium text-gray-300 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date" class="form-input w-full rounded-lg px-3 py-2" value="{{ old('expiry_date') }}">
                <template x-if="errors['expiry_date']">
                    <div class="text-red-500 text-xs mt-2" x-text="errors['expiry_date'][0]"></div>
                </template>
            </div>
        </div>
        <div class="mt-4">
            <label for="notes" class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="form-input w-full rounded-lg px-3 py-2" placeholder="Additional notes about this adjustment...">{{ old('notes') }}</textarea>
            <template x-if="errors['notes']">
                <div class="text-red-500 text-xs mt-2" x-text="errors['notes'][0]"></div>
            </template>
        </div>
    </div>
    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4 pt-2">
        <button type="button" @click="$dispatch('close-modal')" class="btn-secondary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
            Cancel
        </button>
        <button type="submit" class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider" :disabled="loading">
            <template x-if="loading">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
            </template>
            <i class="fas fa-check mr-2" x-show="!loading"></i>
            <span x-text="loading ? 'Saving...' : 'Create Adjustment'"></span>
        </button>
    </div>
</form> 