@extends('layouts.app')

@section('content')
<div>
    <h1 class="section-title">Vendors</h1>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('status') }}</div>
    @endif

    <div class="card">
        <table class="table divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($vendors as $vendor)
                    <tr>
                        <td class="px-4 py-2">{{ $vendor->company_name }}</td>
                        <td class="px-4 py-2">{{ $vendor->user->name ?? '—' }}</td>
                        <td class="px-4 py-2 capitalize">
                            <span class="badge {{ $vendor->status === 'approved' ? 'badge-active' : ($vendor->status === 'blocked' ? 'badge-inactive' : 'badge-pending') }}">{{ $vendor->status }}</span>
                        </td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn-secondary">Approve</button>
                            </form>
                            <form action="{{ route('admin.vendors.block', $vendor) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn-primary">Block</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No vendors found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $vendors->links() }}</div>
</div>
@endsection