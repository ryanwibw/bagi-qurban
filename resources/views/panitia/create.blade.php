<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Tambah Panitia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200 p-8">
                <div x-data="{ mode: 'new' }">
                    <div class="flex space-x-6 mb-8 border-b border-slate-100">
                        <button @click="mode = 'new'" :class="mode === 'new' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-slate-400'" class="pb-3 font-semibold transition-all">
                            Panitia Baru
                        </button>
                        <button @click="mode = 'existing'" :class="mode === 'existing' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-slate-400'" class="pb-3 font-semibold transition-all">
                            Panitia Terdaftar
                        </button>
                    </div>

                    <form method="POST" action="{{ route('panitia.store') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Email (Required for both) -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-slate-700 font-semibold" />
                            <x-text-input id="email" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="email" name="email" :value="old('email')" required />
                            <p class="mt-1 text-xs text-slate-500" x-show="mode === 'existing'">Cukup masukkan email panitia yang sudah terdaftar di masjid lain.</p>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div x-show="mode === 'new'" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Nama Lengkap')" class="text-slate-700 font-semibold" />
                                <x-text-input id="name" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="text" name="name" :value="old('name')" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="password" :value="__('Password')" class="text-slate-700 font-semibold" />
                                    <x-text-input id="password" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="password" name="password" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-slate-700 font-semibold" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="password" name="password_confirmation" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-50">
                            <a href="{{ route('panitia.index') }}" class="text-slate-500 hover:text-slate-700 mr-6 font-medium text-sm transition">Batal</a>
                            <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 py-3 px-10 rounded-xl border-none shadow-lg">
                                {{ __('Tambahkan Panitia') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>