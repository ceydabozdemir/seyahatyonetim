<x-app-layout>
    <x-slot name="header">
        Roller
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">Roller Listesi</h2>
                    @if($roles->isEmpty())
                        <p>Henüz hiç rol bulunmamaktadır.</p>
                    @else
                        <ul class="list-disc pl-5">
                            @foreach($roles as $role)
                                <li>{{ $role->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
