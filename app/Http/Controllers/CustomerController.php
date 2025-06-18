<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Domain;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $selectedDomain = $request->input('domain');

        $customers = Customer::when($selectedDomain, function ($query) use ($selectedDomain) {
                return $query->whereRaw("FIND_IN_SET(?, domains)", [$selectedDomain]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $domains = Domain::all();

        return view('customer.index', compact('customers', 'domains', 'selectedDomain'));
    }
}
