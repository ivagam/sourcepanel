@extends('layout.layout')
@php
    $title = 'Sales Management';
    $subTitle = 'Manage Sales';
    $script = '';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif


<style>
    .modal-body table th,
    .modal-body table td {
        vertical-align: middle !important;
        text-align: left !important;
        padding: 1px 12px !important;
        white-space: nowrap !important;
    }
</style>
<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="row">

         <div class="col-md-4 mb-3">
                <form method="GET" action="{{ route('salesIndex') }}">
                    <div class="input-group">
                        <label class="col-md-4 col-form-label"
                        >Domains </label>
                        <select name="domain" class="form-select" onchange="this.form.submit()">
                            <option value="">-- All Domains --</option>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->domain_id }}" {{ request('domain') == $domain->domain_id ? 'selected' : '' }}>
                                    {{ $domain->domain_name }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('domain'))
                            <a href="{{ route('salesIndex') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sales</h5>
                    </div>
                    <div class="card-body">
                        {{-- Your add/edit form if any --}}
                    </div>
                </div>
            </div>

            {{-- Sales List --}}
            <div class="col-xxl-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sales List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table mb-0" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">S.L</th>
                                        <th>Invoice</th>
                                        <th>First Name</th>
                                        <th>Created</th>
                                        <th>Total</th>                            
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $orders = $sales->groupBy('order_id');
                                    @endphp

                                    @forelse($orders as $orderId => $groupedSales)
                                        @php
                                            $order = $groupedSales->first()->order;
                                            $customer = $order->customer;
                                            $items = $order->items;
                                            $subtotal = $order->subtotal;
                                        @endphp
                                        <tr>
                                            
                                            <td style="text-align: left;">{{ $loop->iteration }}</td>                                            
                                            <td><a href="{{ route('salesInvoice',$order->id) }}" style="text-decoration:underline">#{{ $order->id }}</a></td>
                                            <td>
                                                <button class="text-primary-600" data-bs-toggle="modal" data-bs-target="#customerModal{{ $customer->id }}">
                                                    {{ $customer->first_name ?? '-' }}
                                                </button>
                                            </td>
                                            <td>{{ $customer->created_at ? $customer->created_at->format('Y-m-d') : '-' }}</td>
                                            <td>{{ number_format($subtotal, 2) }}</td>                            
                                            <td>                                                
                                                <button class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#saleModal{{ $orderId }}">
                                                    <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                                </button>


                                                <!-- Order Details Modal -->
                                                <div class="modal fade" id="saleModal{{ $orderId }}" tabindex="-1" aria-labelledby="saleModalLabel{{ $orderId }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h6 class="modal-title" id="saleModalLabel{{ $orderId }}">
                                                                    Order Details for {{ $customer->first_name ?? '-' }}
                                                                </h6>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Order ID:</strong> {{ $orderId }}</p>                                                

                                                                @if($items->count())
                                                                    <table class="table table-bordered align-middle text-center" style="table-layout: fixed; width: 100%;">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th style="width: 50px;">S.No</th>
                                                                                <th style="width: 150px;">Image</th>
                                                                                <th>Product</th>
                                                                                <th style="width: 60px;">Qty</th>
                                                                                <th style="width: 100px;">Price</th>
                                                                                <th style="width: 110px;">Total</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($items as $index => $item)
                                                                                <tr>
                                                                                    <td>{{ $index + 1 }}</td>
                                                                                    <td>
                                                                                        @if($item->product && $item->product->images->isNotEmpty())
                                                                                            <img src="{{ asset('public/' . $item->product->images->first()->file_path) }}"
                                                                                                alt="Product Image"
                                                                                                width="60" height="60"
                                                                                                style="object-fit: cover; border-radius: 4px;">
                                                                                        @else
                                                                                            <span class="text-muted small">No Image</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-start">{{ $item->product_name }}</td>
                                                                                    <td>{{ $item->qty }}</td>
                                                                                    <td>{{ number_format($item->price, 2) }}</td>
                                                                                    <td>{{ number_format($item->total_price, 2) }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <td colspan="4"></td>
                                                                                <th class="text-end">Total</th>
                                                                                <th>{{ number_format($subtotal, 2) }}</th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>

                                                                @else
                                                                    <p>No items found for this order.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Customer Details Modal -->
                                                <div class="modal fade" id="customerModal{{ $customer->id }}" tabindex="-1" aria-labelledby="customerModalLabel{{ $customer->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header text-white">
                                                                <h5 class="modal-title" id="customerModalLabel{{ $customer->id }}">
                                                                    Customer: {{ $customer->first_name ?? '-' }}
                                                                </h5>                                                                
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <table class="table table-striped table-bordered" style="table-layout: fixed; width: 100%;">
                                                                    <tbody>
                                                                        <tr>
                                                                            <th>First Name</th>
                                                                            <td>{{ $customer->first_name ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Last Name</th>
                                                                            <td>{{ $customer->last_name ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Email</th>
                                                                            <td>{{ $customer->email ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Phone</th>
                                                                            <td>{{ $customer->phone ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Address Line 1</th>
                                                                            <td>{{ $customer->address_line1 ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Address Line 2</th>
                                                                            <td>{{ $customer->address_line2 ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>City</th>
                                                                            <td>{{ $customer->city ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>State</th>
                                                                            <td>{{ $customer->state ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Country</th>
                                                                            <td>{{ $customer->country ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Created At</th>
                                                                            <td>{{ $customer->created_at ? $customer->created_at->format('Y-m-d') : '-' }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No sales found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

<script>
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);
</script>
