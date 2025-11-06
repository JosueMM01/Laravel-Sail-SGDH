<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('area')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $areas = Area::all();
        return view('users.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'rol' => 'required|string',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'rol' => $request->rol,
            'area_id' => $request->area_id,
            'password' => null, // ¡CLAVE! Esto permite el login solo con Google al inicio
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario invitado correctamente.');
    }
}
