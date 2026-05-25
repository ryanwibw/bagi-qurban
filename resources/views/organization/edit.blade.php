<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Profil Organisasi') }}
            </h2>
            <a href="{{ route('organization.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none transition ease-in-out duration-150">
                + Tambah Organisasi
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Edit Form -->
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6 border-b pb-2">Edit Detail Organisasi Aktif</h3>
                
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="city" :value="__('Kota')" class="text-slate-700 font-semibold" />
                            <x-text-input id="city" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="text" name="city" :value="old('city', $organization->city)" required />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="owner" :value="__('Pemilik/Admin Utama')" class="text-slate-700 font-semibold" />
                            <x-text-input id="owner" class="block mt-1 w-full bg-slate-50 border-slate-200 rounded-xl opacity-60" type="text" :value="$organization->owner->name" disabled />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Alamat Lengkap')" class="text-slate-700 font-semibold" />
                        <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm">{{ old('address', $organization->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 py-3 px-8 rounded-xl border-none shadow-lg transition-transform active:scale-95">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Organization List -->
        <div class="lg:col-span-1">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6 border-b pb-2">Daftar Organisasi Anda</h3>
                
                <div class="space-y-4">
                    @foreach(auth()->user()->organizations as $org)
                        <form method="POST" action="{{ route('organization.set') }}">
                            @csrf
                            <input type="hidden" name="organization_id" value="{{ $org->id }}">
                            <button type="submit" class="w-full text-left p-4 border rounded-2xl transition-all group {{ session('active_organization_id') == $org->id ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200' : 'border-slate-100 hover:border-emerald-300 hover:bg-slate-50' }}">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold {{ session('active_organization_id') == $org->id ? 'text-emerald-900' : 'text-slate-700' }} group-hover:text-emerald-700">{{ $org->name }}</h4>
                                    @if(session('active_organization_id') == $org->id)
                                        <span class="bg-emerald-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black uppercase">Aktif</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ $org->city }}</p>
                                <p class="text-[10px] font-bold mt-2 uppercase tracking-tighter {{ $org->pivot->role === 'admin' ? 'text-blue-600' : 'text-amber-600' }}">
                                    Role: {{ ucfirst($org->pivot->role) }}
                                </p>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
