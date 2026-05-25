<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pilih Organisasi / Masjid') }}
            </h2>
            <a href="{{ route('organization.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Tambah Organisasi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($organizations as $org)
                        <form method="POST" action="{{ route('organization.set') }}">
                            @csrf
                            <input type="hidden" name="organization_id" value="{{ $org->id }}">
                            <button type="submit" class="w-full text-left p-6 border rounded-lg hover:border-indigo-500 transition-colors group">
                                <h3 class="font-bold text-lg group-hover:text-indigo-600">{{ $org->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $org->city }}</p>
                                <div class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600">
                                    Masuk sebagai {{ ucfirst($org->pivot->role) }}
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
