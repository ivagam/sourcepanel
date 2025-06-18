@extends('layout.layout')
@php
    $title='Dashboard';
    $subTitle = 'CRM';
@endphp

@section('content')


    <div class="row gy-4">
        <div class="col-xxl-12">
            <div class="row gy-4">

                <div class="col-xxl-4 col-sm-6">
                    <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-1">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                        <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">Total Sales</span>
                                        <h6 class="fw-semibold">{{ number_format($totalSales, 2) }}</h6>
                                    </div>
                                </div>

                            </div>                            
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-sm-6">
                    <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-2">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                        <iconify-icon icon="mingcute:user-follow-fill" class="icon"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">Total Product</span>
                                        <h6 class="fw-semibold">{{ $totalProducts }}</h6>
                                    </div>
                                </div>                                
                            </div>                            
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-sm-6">
                    <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-3">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-yellow text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                        <iconify-icon icon="iconamoon:discount-fill" class="icon"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">Total Category</span>
                                        <h6 class="fw-semibold">{{ $totalCategories }}</h6>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
                
                <div class="col-xxl-4 col-sm-6">
                    <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-4">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-purple text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                        <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">Users</span>
                                        <h6 class="fw-semibold">{{ $totalUsers }}</h6>
                                    </div>
                                </div>                                
                            </div>                            
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-sm-6">
                    <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-5">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-pink text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                        <iconify-icon icon="mdi:leads" class="icon"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">Customers</span>
                                        <h6 class="fw-semibold">{{ $totalCustomers }}</h6>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>

            </div>
        </div>
     
        <div class="col-xxl-12 col-xl-12">
                    <div class="card h-100">
                        <div class="card-body p-24">

                            <div class="d-flex flex-wrap align-items-center gap-1 justify-content-between mb-16">
                                <ul class="nav border-gradient-tab nav-pills mb-0" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link d-flex align-items-center active" id="pills-to-do-list-tab" data-bs-toggle="pill" data-bs-target="#pills-to-do-list" type="button" role="tab" aria-controls="pills-to-do-list" aria-selected="true">
                                            Today Sales List
                                        </button>
                                    </li>                                  
                                </ul>                              
                            </div>

                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-to-do-list" role="tabpanel" aria-labelledby="pills-to-do-list-tab" tabindex="0">
                                    <div class="table-responsive scroll-sm">
                                       <table class="table bordered-table sm-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Invoice No</th>
                                                    <th>Customer Name</th>
                                                    <th>Purchase Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($todayOrders as $order)
                                                    <tr>
                                                        <td><a href="{{ route('salesInvoice', $order->id) }}">#{{ $order->id }}</a></td>
                                                        <td>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                                                        <td>₹{{ number_format($order->total, 2) }}</td>                                                       
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No orders today.</td>
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

        <!-- Earning Static start -->
        <div class="col-xxl-12">
            <div class="card h-100 radius-8 border-0">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <div>
                            <h6 class="mb-2 fw-bold text-lg">Earning Statistic</h6>
                            <span class="text-sm fw-medium text-secondary-light">Yearly earning overview</span>
                        </div>
                        <div class="">
                            <select id="salesFilter" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                               <option value="today" selected>Today</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-20 d-flex justify-content-center flex-wrap gap-3">

                        <div id="salesCard" class="d-inline-flex align-items-center gap-2 p-2 radius-8 border pe-36 br-hover-primary group-item">
                            <span class="bg-neutral-100 w-44-px h-44-px text-xxl radius-8 d-flex justify-content-center align-items-center text-secondary-light group-hover:bg-primary-600 group-hover:text-white">
                                <iconify-icon icon="fluent:cart-16-filled" class="icon"></iconify-icon>
                            </span>
                            <div>
                                <span class="text-secondary-light text-sm fw-medium">Sales</span>
                                 <h6 class="text-md fw-semibold mb-0" id="salesAmount">${{ number_format($totalSales) }}</h6>
                            </div>
                        </div>
                        
                    </div>

                    <div id="barChart" class="barChart"></div>
                </div>
            </div>
        </div>
        <!-- Earning Static End -->

        <!-- Campaign Static start -->
        
        <!-- Campaign Static End -->

    </div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let chart = null;

function loadSalesData(filter = 'today') {
    fetch(`/source_panel/dashboard/sales-data?filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('salesAmount').innerText = `$${data.totalSales.toLocaleString()}`;

            const isToday = filter === 'today';

            const options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        columnWidth: isToday ? '15%' : '80%', // Narrower bars for fewer data
                        borderRadius: 4
                    }
                },
                series: [{
                    name: 'Sales',
                    data: data.chartData.series
                }],
                xaxis: {
                    categories: data.chartData.categories
                },
                colors: ['#5A55E0']
            };

            const chartEl = document.querySelector("#barChart");

            if (chart) {
                chart.destroy();
            }

            chart = new ApexCharts(chartEl, options);
            chart.render();
        })
        .catch(error => {
            console.error('Error fetching sales data:', error);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('salesFilter');

    // Initial load (default: today)
    loadSalesData(filterSelect.value);

    // On filter change
    filterSelect.addEventListener('change', function () {
        loadSalesData(this.value);
    });
});

</script>

