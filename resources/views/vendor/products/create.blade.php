@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <h2 class="text-2xl font-semibold mb-4">Add Product</h2>

    <form method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input name="name" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input name="price" type="number" step="0.01" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Stock</label>
                <input name="stock" type="number" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Images</label>
            <input name="images[]" type="file" multiple class="w-full border rounded px-3 py-2">
            <p class="text-xs text-slate-500 mt-1">You can upload multiple images (max 2MB each).</p>
        </div>

        <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Save</button>
    </form>
</div>
@endsection
