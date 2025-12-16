<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(auth()->user()->hasRole('customer'))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-2">Browse Products</h3>
                            <p class="text-slate-600 mb-4">Find items and place orders.</p>
                            <div class="flex gap-3">
                                <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Go to Products</a>
                                <a href="{{ route('customer.orders.index') }}" class="inline-block bg-slate-800 text-white px-4 py-2 rounded hover:bg-slate-900">My Orders</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->hasRole('vendor'))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-2">Vendor Area</h3>
                            <p class="text-slate-600 mb-4">Manage your products and orders.</p>
                            <div class="flex gap-3">
                                <a href="{{ route('vendor.products.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">My Products</a>
                                <a href="{{ route('vendor.orders.index') }}" class="bg-slate-800 text-white px-4 py-2 rounded hover:bg-slate-900">Orders</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->hasRole('admin'))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-2">Admin Area</h3>
                            <p class="text-slate-600 mb-4">Review and approve products.</p>
                            <a href="{{ route('admin.products.pending') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Pending Products</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
