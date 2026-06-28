<x-admin-layout>

    <main class="flex-1 overflow-y-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Add New Coupon</h1>
            <a href="coupons.php"
                class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Coupons
            </a>
        </div>

        <div class="max-w-3xl mx-auto">
            @if (Session::has('success'))
                <div class="px-4 py-3 bg-green-100 text-green-700 text-sm rounded-tl-xl  rounded-tr-xl mb-6">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('admin.coupon.update',['id' => $coupon->id]) }}" method="POST"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Code *</label>
                        <input type="text" id="code" name="code" placeholder="e.g. SUMMER20" required
                            value="{{ old('code',$coupon->code) }}"
                            class="w-full border px-4 py-2 rounded-lg outline-none focus:ring-1 focus:ring-primary uppercase">
                        <p class="text-xs text-gray-500 mt-1">Must be unique.</p>
                        @error('code')
                            <p class="text-red-500 text-xm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type *</label>
                        <select id="type" name="type" required
                            class="w-full border px-4 py-2 rounded-lg bg-white outline-none focus:ring-1 focus:ring-primary">
                            <option value="percent" {{ old('type',$coupon->type) == 'percent' ? 'selected' : '' }}>Percentage (%)
                            </option>
                            <option value="fixed" {{ old('type',$coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value *</label>
                        <input type="number" step="0.01" id="value" name="value" placeholder="e.g. 20"
                            value="{{ old('value',$coupon->value) }}" required
                            class="w-full border px-4 py-2 rounded-lg outline-none focus:ring-1 focus:ring-primary">

                        @error('value')
                            <p class="text-red-500 text-xm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Cart Value *</label>
                        <input type="number" step="0.01" id="cart_value" name="cart_value" placeholder="e.g. 100"
                            value="{{ old('cart_value',$coupon->cart_value) }}" required
                            class="w-full border px-4 py-2 rounded-lg outline-none focus:ring-1 focus:ring-primary">

                        @error('cart_value')
                            <p class="text-red-500 text-xm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date *</label>
                        <input type="date" id="expiry_date" name="expiry_date" required
                            value="{{ old('expiry_date',$coupon->expiry_date) }}"
                            class="w-full border px-4 py-2 rounded-lg outline-none focus:ring-1 focus:ring-primary">

                        @error('expiry_date')
                            <p class="text-red-500 text-xm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.coupons') }}"
                        class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition text-sm">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition text-sm font-medium shadow-sm">Save
                        Coupon</button>
                </div>
            </form>
        </div>
    </main>
    <script>
        // Optional: Force uppercase on code input
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/\s+/g, '');
        });
    </script>
</x-admin-layout>
