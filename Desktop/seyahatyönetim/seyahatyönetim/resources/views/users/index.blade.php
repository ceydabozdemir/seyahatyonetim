@extends('layouts.app')

@section('content')
    @php
        $user = Auth::user();
        $isCalisan = false;

        if ($user) {
            if (isset($user->role) && is_string($user->role)) {
                $isCalisan = ($user->role === 'calisan');
            } elseif (isset($user->role_id)) {
                $isCalisan = ($user->role_id === 2); // 2 = calisan
            } elseif (isset($user->role->name)) {
                $isCalisan = ($user->role->name === 'calisan');
            }
        }

        if ($isCalisan) {
            abort(403, 'Bu sayfaya erişiminiz yok.');
        }
    @endphp

    <div class="container">
        <h1>Kullanıcılar</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Kullanıcı Ekle</a>
        <table class="table mt-3">
            <thead>
            <tr>
                <th>Ad</th>
                <th>Email</th>
                <th>İşlemler</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Düzenle</a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Sil</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
