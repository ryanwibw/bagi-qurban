<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Tambah Panitia Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-8">
        <form method="POST" action="{{ route('panitia.store') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" class="text-slate-700 font-semibold" />
                <x-text-input id="name" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="text" name="name" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-slate-700 font-semibold" />
                <x-text-input id="email" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-slate-700 font-semibold" />
                <x-text-input id="password" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="password" name="password" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-slate-700 font-semibold" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="password" name="password_confirmation" required />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('panitia.index') }}" class="text-slate-500 hover:text-slate-700 mr-4 font-medium text-sm transition">Batal</a>
                <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 py-3 px-8 rounded-xl border-none shadow-lg">
                    {{ __('Simpan Panitia') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
