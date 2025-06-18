<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Domain;
use App\Models\CustomerOrder;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $selectedDomain = $request->input('domain');

        $sales = Sales::with(['order.customer', 'product.images'])
            ->when($selectedDomain, function ($query) use ($selectedDomain) {
                return $query->whereHas('order.customer', function ($q) use ($selectedDomain) {
                    $q->whereRaw("FIND_IN_SET(?, domains)", [$selectedDomain]);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        //print_r($sales); exit;
        $domains = Domain::all();

        return view('sales.index', compact('sales', 'domains', 'selectedDomain'));
    }
    
    public function salesInvoice($id)
    {          
        $sale = CustomerOrder::with(['customer', 'items.product'])->find($id);
        //print_r($sale); exit;
        return view('sales.salesInvoice', compact('sale'));
    }
}
