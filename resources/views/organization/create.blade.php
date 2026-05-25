<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Daftarkan Organisasi / Masjid Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200 p-8">
                <form method="POST" action="{{ route('organization.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Nama Organisasi (Masjid/Yayasan)')" class="text-slate-700 font-semibold" />
                        <x-text-input id="name" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="city" :value="__('Kota/Kabupaten')" class="text-slate-700 font-semibold" />
                        <x-text-input id="city" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="text" name="city" :value="old('city')" required />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Alamat Lengkap')" class="text-slate-700 font-semibold" />
                        <textarea id="address" name="address" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm min-h-[100px]" required>{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-50">
                        <a href="{{ route('organization.select') }}" class="text-slate-500 hover:text-slate-700 mr-6 font-medium text-sm transition">Batal</a>
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 py-3 px-10 rounded-xl border-none shadow-lg">
                            {{ __('Daftarkan Sekarang') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
