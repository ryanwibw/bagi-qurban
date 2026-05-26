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
                        <div class="relative p-4 border rounded-2xl transition-all group {{ session('active_organization_id') == $org->id ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200' : 'border-slate-100 hover:border-emerald-300 hover:bg-slate-50' }}">
                            
                            <!-- 1. Form Switch (Tombol utama yang menutupi area) -->
                            <form method="POST" action="{{ route('organization.set') }}" class="absolute inset-0 z-0">
                                @csrf
                                <input type="hidden" name="organization_id" value="{{ $org->id }}">
                                <button type="submit" class="w-full h-full cursor-pointer"></button>
                            </form>

                            <!-- 2. Isi Informasi & Tombol Hapus (Z-index lebih tinggi agar bisa diklik) -->
                            <div class="relative z-10 flex justify-between items-start pointer-events-none">
                                <div>
                                    <h4 class="font-bold {{ session('active_organization_id') == $org->id ? 'text-emerald-900' : 'text-slate-700' }} group-hover:text-emerald-700">{{ $org->name }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">{{ $org->city }}</p>
                                    <p class="text-[10px] font-bold mt-2 uppercase tracking-tighter {{ $org->pivot->role === 'admin' ? 'text-blue-600' : 'text-amber-600' }}">
                                        Role: {{ ucfirst($org->pivot->role) }}
                                    </p>
                                </div>
                                
                                <div class="flex gap-2 pointer-events-auto">
                                    @if(session('active_organization_id') == $org->id)
                                        <span class="bg-emerald-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black uppercase">Aktif</span>
                                    @endif
                                    
                                    @if($org->owner_id == auth()->id())
                                        <!-- Form Hapus Terpisah -->
                                        <form method="POST" action="{{ route('organization.destroy', $org) }}" id="delete-form-{{ $org->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="openDeleteModal('{{ $org->id }}', '{{ $org->name }}')" class="text-slate-400 hover:text-red-600 transition-colors" title="Hapus Organisasi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h14"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl transform transition-all">
            <h3 class="text-xl font-black text-slate-800">Hapus Organisasi?</h3>
            <p class="text-slate-500 mt-2 text-sm">Organisasi <span id="org-name-placeholder" class="font-bold text-slate-700"></span> dan seluruh data kupon di dalamnya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</p>
            
            <div class="flex gap-3 mt-8">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">Batal</button>
                <button id="confirm-delete-btn" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition shadow-lg shadow-red-200">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('delete-modal');
        const orgNamePlaceholder = document.getElementById('org-name-placeholder');
        const confirmBtn = document.getElementById('confirm-delete-btn');
        let currentOrgId = null;

        function openDeleteModal(orgId, orgName) {
            currentOrgId = orgId;
            orgNamePlaceholder.innerText = orgName;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            modal.classList.add('hidden');
        }

        confirmBtn.addEventListener('click', function() {
            if (currentOrgId) {
                document.getElementById('delete-form-' + currentOrgId).submit();
            }
        });
    </script>
</x-app-layout>
