@extends('layout.layout')
@php
    $title = 'Customer Management';
    $subTitle = 'Manage Customer';
    $script = '';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="row">

            {{-- Domain Filter Dropdown --}}
            <div class="col-md-4 mb-3">
                <form method="GET" action="{{ route('customerIndex') }}">
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
                            <a href="{{ route('customerIndex') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Customer List --}}
            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table mb-0" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">S.L</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>Country</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $key => $customer)
                                        <tr>
                                            <td style="text-align: left;">{{ $key + 1 }}</td>
                                            <td>{{ $customer->first_name }}</td>
                                            <td>{{ $customer->last_name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->phone }}</td>
                                            <td>{{ $customer->city }}</td>
                                            <td>{{ $customer->country }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No customers found.</td></tr>
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
