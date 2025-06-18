@extends('layout.layout')

@php
    $title = 'Invoice';
    $subTitle = 'Invoice';
    $script = '<script>
        function printInvoice() {
            var printContents = document.getElementById("invoice").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>';
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                   
            <button type="button" class="btn btn-sm btn-danger radius-8 d-inline-flex align-items-center gap-1" onclick="printInvoice()">
                <iconify-icon icon="basil:printer-outline" class="text-xl"></iconify-icon> Print
            </button>
        </div>
    </div>

    <div class="card-body py-40">
        <div class="row justify-content-center" id="invoice">
            <div class="col-lg-12">
                <div class="shadow-4 border radius-8">
                    <div class="p-20 d-flex flex-wrap justify-content-between gap-3 border-bottom">
                        
                        <div>
                            <h3 class="text-xl">Invoice #{{ $sale->id ?? 'N/A' }}</h3>
                            <p class="mb-1 text-sm">Date Issued: {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</p>
                            <!--<p class="mb-0 text-sm">Due Date: {{ \Carbon\Carbon::parse($sale->due_date ?? $sale->order_date)->format('d/m/Y') }}</p>-->
                        </div>
                        <div>
                            <img src="{{ asset('assets/images/logo-light.png') }}" alt="logo" class="mb-8" width="200">
                            <p class="mb-1 text-sm">4517 Washington Ave. Manchester, Kentucky 39495</p>
                            <p class="mb-0 text-sm">random@gmail.com, +1 543 2198</p>
                        </div>
                    </div>

                    <div class="py-28 px-20">
                        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                            <div>
                                <h6 class="text-md">Issued For:</h6>
                                <table class="text-sm text-secondary-light">
                                    <tbody>
                                        <tr>
                                            <td>Name</td>
                                            <td class="ps-8">
                                                : {{ $sale->customer->first_name ?? '-' }}
                                            </td>
                                        <tr>
                                            <td>Address</td>
                                            <td class="ps-8">
                                                : {{ $sale->customer->address_line1 ?? '-' }}
                                            </td>
                                        <tr>
                                            <td>Phone number</td>
                                            <td class="ps-8">
                                                : {{ $sale->customer->phone ?? '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <table class="text-sm text-secondary-light">
                                    <tbody>
                                        <tr>
                                            <td>Issue Date</td>
                                            <td class="ps-8">: {{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Order ID</td>
                                            <td class="ps-8">: {{ $sale->id ?? '-' }}</td>
                                        </tr>                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-24">
                            <div class="table-responsive scroll-sm">
                                <table class="table bordered-table text-sm">
                                    <thead>
                                        <tr>
                                            <th scope="col">SL.</th>
                                            <th scope="col">Product Name</th>                                            
                                            <th scope="col">Qty</th>
                                            <th scope="col">price</th>
                                            <th scope="col" class="text-end">Total Price</th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                        @php $totalAmount = 0; @endphp
                                        @foreach($sale->items as $key => $item)
                                            @php $totalAmount += $item->total_price; @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->product->product_name ?? $item->product_name }}</td>
                                                <td>{{ $item->qty }}</td>
                                                <td>{{ number_format($item->price, 2) }}</td>
                                                <td class="text-end">{{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between gap-3 mt-24">
                                <div>
                                    <p class="text-sm mb-0"><span class="text-primary-light fw-semibold">Sales By:</span> {{ $sale->sales_by ?? 'Admin' }}</p>
                                    <p class="text-sm mb-0">Thanks for your business</p>
                                </div>

                                <div>
                                    <table class="text-sm">
                                        <tbody>
                                            <tr>
                                                <td class="pe-64">Subtotal:</td>
                                                <td class="pe-16">
                                                <span class="text-primary-light">
                                                    {{ number_format($totalAmount, 2) }}
                                                </span>
                                                </td>
                                            </tr>
                                            <!--<tr>
                                                <td class="pe-64">Discount:</td>
                                                <td class="pe-16">
                                                    <span class="text-primary-light fw-semibold">${{ number_format($sale->discount ?? 0, 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pe-64 border-bottom pb-4">Tax:</td>
                                                <td class="pe-16 border-bottom pb-4">
                                                    <span class="text-primary-light fw-semibold">${{ number_format($sale->tax ?? 0, 2) }}</span>
                                                </td>
                                            </tr>-->
                                            <tr>
                                                <td class="pe-64 pt-4"><span class="text-primary-light fw-semibold">Total:</span></td>
                                                <td class="pe-16 pt-4">
                                                    <span class="text-primary-light fw-semibold">{{ number_format($totalAmount, 2) }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-64">
                                <p class="text-center text-secondary-light text-sm fw-semibold">Thank you for your purchase!</p>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-end mt-64">
                                <div class="text-sm border-top d-inline-block px-12">Signature of Customer</div>
                                <div class="text-sm border-top d-inline-block px-12">Signature of Authorized</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function downloadPDF() {
    const element = document.getElementById('invoice');
html2pdf().set({
    margin:       0.5,
    filename:     'invoice.pdf',
    image:        { type: 'jpeg', quality: 0.98 },
    html2canvas:  { scale: 2 },
    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
}).from(element).save();

}

async function saveAndSendWhatsApp() {
    const { jsPDF } = window.jspdf;
    const invoiceElement = document.getElementById("invoice");

    const canvas = await html2canvas(invoiceElement);
    const imgData = canvas.toDataURL("image/png");

    const pdf = new jsPDF();
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();

    // Define margins (in mm)
    const marginLeft = 10;
    const marginTop = 10;
    const marginRight = 10;

    const usableWidth = pageWidth - marginLeft - marginRight;
    const scaledHeight = (canvas.height * usableWidth) / canvas.width;

    // Add image with margins
    pdf.addImage(imgData, 'PNG', marginLeft, marginTop, usableWidth, scaledHeight);

    const blob = pdf.output("blob");

    const formData = new FormData();
    formData.append("file", blob, "invoice.pdf");
    formData.append("_token", "{{ csrf_token() }}");

    const response = await fetch("{{ url('save-invoice') }}", {
        method: "POST",
        body: formData
    });

    const result = await response.json();

    if (result.success) {
        const whatsappURL = `https://wa.me/?text=${encodeURIComponent("Here is your invoice: " + result.url)}`;
        window.open(whatsappURL, "_blank");
    } else {
        alert("Failed to upload PDF");
    }
}

</script>
