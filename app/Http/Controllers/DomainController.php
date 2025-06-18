<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::orderBy('created_at', 'desc')->paginate(10);
        return view('domain.index', compact('domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'domain_name' => 'required|string|max:255',
        ]);

        Domain::create([
            'domain_name' => $request->domain_name,
            'created_by' => session('user_id'),
        ]);

        return redirect()->route('domainIndex')->with('success', 'Domain added successfully.');
    }

    public function edit($id)
    {
        $editdomain = Domain::findOrFail($id);
        $domains = Domain::orderBy('created_at', 'desc')->paginate(10);
        return view('domain.index', compact('editdomain', 'domains'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'domain_name' => 'required|string|max:255',
        ]);

        Domain::where('domain_id', $id)->update([
            'domain_name' => $request->domain_name,
            'created_by' => session('user_id'),
        ]);

        return redirect()->route('domainIndex')->with('success', 'Domain updated successfully.');
    }

    public function destroy($id)
    {
        Domain::where('domain_id', $id)->delete();
        return redirect()->route('domainIndex')->with('success', 'Domain deleted successfully.');
    }
}
