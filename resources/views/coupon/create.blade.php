<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Ajukan Kupon Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-8">
        <form method="POST" action="{{ route('coupon.store') }}" class="space-y-6">
            @csrf

            <!-- Number of Coupons -->
            <div>
                <x-input-label for="count" :value="__('Jumlah Kupon yang Dibuat')" class="text-slate-700 font-semibold" />
                <x-text-input id="count" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="number" name="count" :value="old('count', 10)" required min="1" autofocus />
                <x-input-error :messages="$errors->get('count')" class="mt-2" />
            </div>

            <!-- Weight per Coupon -->
            <div>
                <x-input-label for="weight_kg" :value="__('Berat Daging per Kupon (Kg)')" class="text-slate-700 font-semibold" />
                <x-text-input id="weight_kg" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" type="number" step="0.1" name="weight_kg" :value="old('weight_kg', 1.0)" required min="0.1" />
                <x-input-error :messages="$errors->get('weight_kg')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('coupon.index') }}" class="text-slate-500 hover:text-slate-700 mr-4 font-medium text-sm transition">Batal</a>
                <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 py-3 px-8 rounded-xl border-none shadow-lg">
                    {{ __('Generate Kupon') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
