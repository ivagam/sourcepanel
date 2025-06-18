<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function addUser()
    {
        return view('users.addUser');
    }
    
    public function usersList()
{
    $users = User::paginate(10);

    return view('users.usersList', compact('users'));
}

    public function store(Request $request)
{
    $request->validate([
        'username'   => 'required|string|max:255|unique:admins',
        'password'   => 'required|string|min:6',
        'firstname'  => 'required|string|max:255',
        'lastname'   => 'required|string|max:255',
        'email'      => 'required|email|unique:admins',
        'phone'      => 'required|string|max:20',
        'profile'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user = new User();
    $user->username = $request->username;
    $user->password = Hash::make($request->password);
    $user->firstname = $request->firstname;
    $user->lastname = $request->lastname;
    $user->email = $request->email;
    $user->phone = $request->phone;

    if ($request->hasFile('profile')) {
        $filename = time() . '_' . $request->file('profile')->getClientOriginalName();
        $request->file('profile')->move(public_path('uploads'), $filename);
        $user->profile = 'uploads/' . $filename;
    }

    $user->save();

    return redirect()->back()->with('success', 'User created successfully!');
}

public function editUser($id)
{
    $user = User::findOrFail($id);
    return view('users.editUser', compact('user'));
}

public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'username'   => 'required|string|max:255|unique:admins,username,' . $user->id,        
        'firstname'  => 'required|string|max:255',
        'lastname'   => 'required|string|max:255',
        'email'      => 'required|email|unique:admins,email,' . $user->id,
        'phone'      => 'required|string|max:20',
        'profile'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user->username = $request->username;    
    $user->firstname = $request->firstname;
    $user->lastname = $request->lastname;
    $user->email = $request->email;
    $user->phone = $request->phone;

    if ($request->hasFile('profile')) {
        $filename = time() . '_' . $request->file('profile')->getClientOriginalName();
        $request->file('profile')->move(public_path('uploads'), $filename);
        $user->profile = 'uploads/' . $filename;
    }

    $user->save();
    
    return redirect()->route('usersList')->with('success', 'User updated successfully!');

}

public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('usersList')->with('success', 'User deleted successfully!');
    }

    public function profile($id)
    {
        $user = User::findOrFail($id);
        return view('users.viewProfile', compact('user'));
    }


public function changePassword(Request $request)
{
    $request->validate([
        'password' => 'required|string|confirmed|min:6',
    ]);

    $user = auth()->user();
    $user->password = Hash::make($request->password);
    $user->save();

    return redirect()->back()->with('success', 'Password updated successfully!');
}
}
