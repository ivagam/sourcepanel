<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Whatsapp;

class WhatsappController extends Controller
{
    public function index()
    {
        $whatsappMessages = Whatsapp::orderBy('created_at', 'desc')->get();
        return view('whatsapp.index', compact('whatsappMessages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message'  => 'required|string',
            'shortcut' => 'required|string|max:50',
        ]);

        Whatsapp::create([
            'message'  => $request->message,
            'shortcut' => $request->shortcut,
        ]);

        return redirect()->route('whatsappIndex')->with('success', 'WhatsApp message saved successfully.');
    }

    public function destroy($id)
    {
        $whatsapp = Whatsapp::findOrFail($id);
        $whatsapp->delete();

        return redirect()->route('whatsappIndex')->with('success', 'WhatsApp message deleted successfully.');
    }
}
