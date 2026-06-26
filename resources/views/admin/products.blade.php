<x-admin-layout>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-100">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Products</h1>
                <p class="text-sm text-gray-500">Manage your product catalog</p>
            </div>
            <button type="button" id="bulkDeleteBtn"
                class="hidden bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
            </button>
            <a href="{{ route('admin.product.add') }}"
                class="bg-primary hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i> Add New Product
            </a>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="flex flex-col md:flex-row gap-4 justify-between">
                <form method="GET" action={{ url()->current() }} id="filterForm" class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" onkeypress="if(event.key=== 'Enter') this.fomr.submit()"
                            class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                            placeholder="Search product name...">
                    </div>

                    <select name="category" onchange="this.form.submit()"
                        class="w-full md:w-48 border px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-primary bg-white text-gray-600">
                        <option value="">Select Category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select name="brand" onchange="this.form.submit()"
                        class="w-full md:w-48 border px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-primary bg-white text-gray-600">
                        <option value="">Select Brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}</option>
                        @endforeach
                    </select>

                    <select name="status" onchange="this.form.submit()"
                        class="w-full md:w-40 border px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-primary bg-white text-gray-600">
                        <option value="">All Status</option>
                        <option value="0" {{ request('status')|| request()->filled('status') ? 'selected' : '' }}>Draft</option>
                        <option value="1" {{ request('status') == 1 ? 'selected' : '' }}>Published</option>
                    </select>
                </form>

                @if (request()->hasAny(['search', 'category', 'brand', 'status']))
                    <a href="{{ route('admin.products') }}" class="text-gray-500 hover:underline text-sm font-medium self-center">
                        Clear Filters
                    </a>
                @endif
                <button
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-export"></i> Export
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <form id="bulkActionForm" method="POST" action={{ route('admin.products.bulk.delete') }}>
                @csrf
                @method('DELETE')
                <div class="overflow-x-auto">
                    @if (session('success'))
                        <div class="px-6 py-4 bg-green-100 text-green-700 text-sm rounded-tl-xl  rounded-tr-xl ">
                            {{ session('success') }}
                        </div>
                    @endif
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-4">
                                    <input type="checkbox" id="selectAll"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                </th>
                                <th class="px-6 py-4">Product Name</th>
                                <th class="px-6 py-4">Brand</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Price</th>
                                <th class="px-6 py-4">Stock</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($products as $pro)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="ids[]" value={{ $pro->id }}
                                            class="product-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ asset('uploads/products/thumbnails') }}/{{ $pro->image }}"
                                                class="w-12 h-12 rounded object-cover border"  onerror="this.src='https://placehold.co/40x40?text=NO-IMG'"
                                                alt="{{ $pro->name }}">
                                            <div>
                                                <p class="font-semibold text-gray-800 text-sm">{{ $pro->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $pro->SKU }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $pro->brand->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $pro->category->name }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        @if ($pro->sale_price)
                                            <span
                                                class="lien-through text-gray-400 mr-2">{{ number_format($pro->regular_price, 2) }}</span>
                                            <span class="text-primary">{{ number_format($pro->sale_price, 2) }}</span>
                                        @else
                                            <span
                                                class="text-gray-800">{{ number_format($pro->regular_price, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($pro->quantity, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                                            @if ($pro->status)
                                                Published
                                            @else
                                                Draft
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.product.edit', ['id' => $pro->id]) }}"
                                                class="w-8 h-8 rounded-full hover:bg-gray-100 text-blue-500 transition flex items-center justify-center"
                                                title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <form id="delete-form-{{ $pro->id }}" method="POST"
                                                action="{{ route('admin.product.delete', ['id' => $pro->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="w-8 h-8 rounded-full hover:bg-gray-100 text-red-500 transition flex items-center justify-center"
                                                    onclick="deletePro(this, '{{ $pro->name }}', {{ $pro->id }})"
                                                    title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <i class="fa-solid fa-boxes-stacked text-4xl mb-3 text-gray-300"></i>
                                            <h3 class="text-lg font-medium text-gray-900">Products not available</h3>
                                            <p class="text-sm mt-1">You haven't added any products to your store yet.
                                            </p>
                                            <a href="{{ route('admin.product.add') }}"
                                                class="mt-4 text-primary hover:underline text-sm font-medium">
                                                Add your first product
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div
                class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                {{ $products->links() }}
            </div>
        </div>

        <div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Delete
                                        Product</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Are you sure you want to delete <strong
                                                id="delete-name" class="text-gray-800">this product</strong>? All
                                            of
                                            its data will be permanently removed. This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" id="confirmDeleteBtn"
                                class="inline-flex w-full justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition">Delete</button>
                            <button type="button" id="cancelDeleteBtn"
                                class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</x-admin-layout>


<script>
    // Modal Elements
    const deleteModal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const brandNameSpan = document.getElementById('delete-name');

    // Variables to hold the state of what we are deleting
    let rowToDelete = null;
    let brandIdToDelete = null;

    // Function triggered when the trash icon is clicked
    function deletePro(buttonElement, brandName, brandId) {
        // Save the row so we can remove it later
        rowToDelete = buttonElement.closest('tr');
        brandIdToDelete = brandId;

        // Update the modal text dynamically
        brandNameSpan.textContent = brandName || "this product";

        // Show the modal by removing the 'hidden' class
        deleteModal.classList.remove('hidden');
    }

    // Close Modal Function
    function closeModal() {
        deleteModal.classList.add('hidden');
        rowToDelete = null;
        brandIdToDelete = null;
    }

    // Handle Cancel Button
    cancelBtn.addEventListener('click', closeModal);

    // Handle clicking outside the modal to close it
    deleteModal.addEventListener('click', function(event) {
        // If the user clicks on the backdrop (not the panel), close it
        if (event.target === this || event.target.classList.contains('bg-opacity-75')) {
            closeModal();
        }
    });

    // Handle Confirm Delete Button
    confirmBtn.addEventListener('click', function() {

        if (brandIdToDelete) {
            const form = document.getElementById(`delete-form-${brandIdToDelete}`);
            if (form) {
                form.submit();
            }
        }
    });


    // Delet products
    const selectAll = document.getElementById('selectAll');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActionForm = document.getElementById('bulkActionForm');
    const checkboxes = document.querySelectorAll('.product-checkbox');

    //Toggle all elements
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkBtn();
    })

    //toggle indiviusal checkbox
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBtn);
    });

    function updateBulkBtn() {
        const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount;

        if (checkedCount > 0) {
            bulkDeleteBtn.classList.remove('hidden');
        } else {
            bulkDeleteBtn.classList.add('hidden');
            selectAll.checked = false;
        }
    }

    //delete confirmation
    bulkDeleteBtn.addEventListener('click', () => {
        const count = document.querySelectorAll('.product-checkbox:checked').length;

        if (confirm(`Are you sure you want to delete ${count} selected products?`)) {
            bulkActionForm.submit();
        }

    })
</script>
