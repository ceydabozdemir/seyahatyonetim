<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// 👇 Bu satırı ekle
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Kullanıcıları listele
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // Yeni kullanıcı oluşturma formu
    public function create()
    {
        return view('users.create');
    }

    // Kullanıcıyı kaydet
    public function store(Request $request)
    {
        // Validation işlemi
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        // Kullanıcıyı oluştur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 🔐 Rol ata: yeni kullanıcıya otomatik olarak "calisan" rolünü ver
        $user->assignRole('calisan');

        return redirect()->route('users.index');
    }

    // Kullanıcı düzenleme formu
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // Kullanıcıyı güncelle
    public function update(Request $request, User $user)
    {
        // Validation işlemi
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Şifreyi kontrol et ve hashle
        $password = $request->password ? Hash::make($request->password) : $user->password;

        // Kullanıcıyı güncelle
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
        ]);

        return redirect()->route('users.index');
    }

    // Kullanıcı sil
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}
