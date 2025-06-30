<x-app-layout>
    <x-slot name="header">
        İzinler
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">İzinler Listesi</h2>

                    @if (session('success'))
                        <div class="text-green-600 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <a href="{{ route('permissions.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Yeni İzin Ekle</a>

                    @if($permissions->isEmpty())
                        <p>Henüz hiç izin bulunmamaktadır.</p>
                    @else
                        <table class="table-auto w-full text-left">
                            <thead>
                            <tr>
                                <th class="px-4 py-2">İzin Adı</th>
                                <th class="px-4 py-2">Atandığı Roller</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td class="border px-4 py-2">{{ $permission->name }}</td>
                                    <td class="border px-4 py-2">
                                        @if($permission->roles->isNotEmpty())
                                            @foreach($permission->roles as $role)
                                                <span class="inline-block bg-gray-200 px-2 py-1 rounded mr-2">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <em>Henüz rol atanmamış</em>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
