<!-- resources/views/users/create.blade.php -->

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Ekle</title>
</head>
<body>
<h1>Kullanıcı Ekle</h1>

<form action="{{ route('users.store') }}" method="POST">
    @csrf
    <div>
        <label for="name">Ad:</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        @error('name')
        <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="email">E-posta:</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        @error('email')
        <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="password">Şifre:</label>
        <input type="password" name="password" id="password" required>
        @error('password')
        <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="password_confirmation">Şifre Tekrarı:</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
    </div>

    <div>
        <button type="submit">Kullanıcı Ekle</button>
    </div>
</form>
</body>
</html>
