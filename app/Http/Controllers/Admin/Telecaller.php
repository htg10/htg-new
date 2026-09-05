<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Telecaller extends Controller
{

    public function index(Request $request)
    {
        $telecallers = User::where('role_id', 3)->orderBy('id', 'desc')
            ->get();

        return view('admin.telecaller.index', compact('telecallers'));
    }

    public function create()
    {
        return view('admin.telecaller.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
        ]);
        // dd($request->all());
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3,
        ]);
        return redirect()->route('admin.telecallers')->with('success', 'Telecaller Create successful.');
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
    }
}
