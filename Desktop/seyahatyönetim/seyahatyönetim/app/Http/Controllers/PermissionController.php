<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        // İzinleri ve ilişkili rolleri getir
        $permissions = Permission::with('roles')->get();

        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        // Tüm rolleri getir, kullanıcı seçebilsin
        $roles = Role::all();

        return view('permissions.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Formdan gelen veriyi doğrula
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        // Yeni izin oluştur
        $permission = Permission::create(['name' => $request->name]);

        // Eğer rol seçilmişse, o role izni ata
        if ($request->filled('role_id')) {
            $role = Role::findById($request->role_id);
            $role->givePermissionTo($permission);
        }

        return redirect()->route('permissions.index')->with('success', 'İzin başarıyla oluşturuldu.');
    }
}
