<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Profil Organisasi') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('organization.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="name" :value="__('Nama Organisasi / Masjid')" class="text-slate-700 font-semibold" />
                <x-text-input id="name" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="text" name="name" :value="old('name', $organization->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="city" :value="__('Kota')" class="text-slate-700 font-semibold" />
                <x-text-input id="city" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="text" name="city" :value="old('city', $organization->city)" required />
                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="address" :value="__('Alamat Lengkap')" class="text-slate-700 font-semibold" />
                <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm">{{ old('address', $organization->address) }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 py-3 px-8 rounded-xl border-none shadow-lg">
                    {{ __('Simpan Perubahan') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
