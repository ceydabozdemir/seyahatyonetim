<x-app-layout>
    <x-slot name="header">
        Yeni İzin Oluştur
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow rounded-lg">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block font-bold mb-2">İzin Adı</label>
                        <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" required>
                    </div>

                    <div class="mb-4">
                        <label for="role_id" class="block font-bold mb-2">Role Ata (Opsiyonel)</label>
                        <select name="role_id" id="role_id" class="w-full border px-3 py-2 rounded">
                            <option value="">-- Seç --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
