<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('logo-bagi-qurban.png') }}" alt="Logo" class="h-8 w-8 rounded-lg">
                        <span class="font-bold text-xl tracking-tight text-emerald-900">Bagi Qurban</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-600 hover:text-emerald-600">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('coupon.index')" :active="request()->routeIs('coupon.*')" class="text-slate-600 hover:text-emerald-600">
                        {{ __('Kupon') }}
                    </x-nav-link>
                    <x-nav-link :href="route('recap.index')" :active="request()->routeIs('recap.*')" class="text-slate-600 hover:text-emerald-600">
                        {{ __('Rekapitulasi') }}
                    </x-nav-link>
                    @if(Auth::user()->isAdmin())
                    <x-nav-link :href="route('panitia.index')" :active="request()->routeIs('panitia.*')" class="text-slate-600 hover:text-emerald-600">
                        {{ __('Panitia') }}
                    </x-nav-link>
                    <x-nav-link :href="route('organization.edit')" :active="request()->routeIs('organization.edit')" class="text-slate-600 hover:text-emerald-600">
                        {{ __('Organisasi') }}
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @php
                    $activeOrgId = session('active_organization_id');
                    $activeOrg = \App\Models\Organization::find($activeOrgId);
                    $role = auth()->user()->roleInOrganization($activeOrgId);
                @endphp
                
                <div class="mr-4 flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-2xl px-3 py-1.5">
                    <div class="text-right hidden lg:block">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Organisasi Aktif</p>
                        <p class="text-sm font-black text-emerald-700 leading-tight truncate max-w-[150px]">{{ $activeOrg->name ?? 'None' }}</p>
                    </div>
                    
                    @if(auth()->user()->organizations()->count() > 1)
                    <form action="{{ route('organization.set') }}" method="POST" class="border-l border-slate-200 pl-3">
                        @csrf
                        <select name="organization_id" onchange="this.form.submit()" class="text-xs font-bold border-none focus:ring-0 bg-transparent py-0 pl-0 pr-6 text-slate-600 cursor-pointer hover:text-emerald-600 transition-colors">
                            @foreach(auth()->user()->organizations as $org)
                                <option value="{{ $org->id }}" {{ session('active_organization_id') == $org->id ? 'selected' : '' }}>
                                    Pindah Ke: {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-500 bg-white hover:text-slate-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} ({{ ucfirst($role) }})</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(auth()->user()->organizations()->count() > 1)
                        <x-dropdown-link :href="route('organization.select')">
                            {{ __('Ganti Organisasi') }}
                        </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-slate-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('coupon.index')" :active="request()->routeIs('coupon.*')">
                {{ __('Kupon') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('recap.index')" :active="request()->routeIs('recap.*')">
                {{ __('Rekapitulasi') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-200">
            <div class="px-4 text-sm font-medium text-slate-500">
                <div class="text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
