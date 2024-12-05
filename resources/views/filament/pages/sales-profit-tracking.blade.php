<x-filament::page>
    <div class="space-y-8">
        {{-- Improved Header and Timeframe Filter --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
{{--            <div class="flex items-center space-x-4">--}}
{{--                <h1 class="text-3xl font-extrabold text-gray-900">Laporan Penjualan</h1>--}}
{{--                <x-filament::badge--}}
{{--                    color="success"--}}
{{--                    size="lg">--}}
{{--                    {{ $selectedTimeframePeriod }}--}}{{-- TEST--}}
{{--                </x-filament::badge>--}}
{{--            </div>--}}

            <div class="w-full md:w-64">
                <x-filament::input.wrapper>
                    <x-filament::input.select
                        wire:model.live="timeframe"
                        label="Rentang Waktu"
                        class="border-primary-600 focus:ring-primary-500">
                        @foreach ($timeframes as $frame)
                            <option value="{{ $frame }}">{{ ucfirst($frame) }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>

{{--        --}}{{-- Summary Cards --}}
{{--        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">--}}
{{--            <x-filament::card>--}}
{{--                <div class="flex justify-between items-center">--}}
{{--                    <div>--}}
{{--                        <p class="text-sm text-gray-500">Total Pendapatan</p>--}}
{{--                        <h2 class="text-2xl font-bold text-green-600">--}}
{{--                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}--}}
{{--                        </h2>--}}
{{--                    </div>--}}
{{--                    <x-filament::icon--}}
{{--                        name="heroicon-o-chart-bar"--}}
{{--                        class="h-8 w-8 text-green-500" />--}}
{{--                </div>--}}
{{--            </x-filament::card>--}}

{{--            <x-filament::card>--}}
{{--                <div class="flex justify-between items-center">--}}
{{--                    <div>--}}
{{--                        <p class="text-sm text-gray-500">Total Biaya</p>--}}
{{--                        <h2 class="text-2xl font-bold text-red-600">--}}
{{--                            Rp {{ number_format($totalCOGS, 0, ',', '.') }}--}}
{{--                        </h2>--}}
{{--                    </div>--}}
{{--                    <x-filament::icon--}}
{{--                        name="heroicon-o-currency-dollar"--}}
{{--                        class="h-8 w-8 text-red-500" />--}}
{{--                </div>--}}
{{--            </x-filament::card>--}}

{{--            <x-filament::card>--}}
{{--                <div class="flex justify-between items-center">--}}
{{--                    <div>--}}
{{--                        <p class="text-sm text-gray-500">Keuntungan Bersih</p>--}}
{{--                        <h2 class="text-2xl font-bold {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">--}}
{{--                            Rp {{ number_format($totalProfit, 0, ',', '.') }}--}}
{{--                        </h2>--}}
{{--                    </div>--}}
{{--                    <x-filament::icon--}}
{{--                        name="heroicon-o-trending-up"--}}
{{--                        class="h-8 w-8 {{ $totalProfit >= 0 ? 'text-green-500' : 'text-red-500' }}" />--}}
{{--                </div>--}}
{{--            </x-filament::card>--}}
{{--        </div>--}}

        {{-- Sales Table --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-primary-100 border-b border-primary-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">
                        Pendapatan
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">
                        Biaya
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">
                        Keuntungan
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-primary-700 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse ($revenueData as $date => $data)
                    <tr class="hover:bg-primary-50 transition duration-150
                            {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $date }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-green-600 font-semibold">
                            Rp {{ number_format($data['summary']['revenue'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-red-600">
                            Rp {{ number_format($data['summary']['cogs'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-right
                                {{ $data['summary']['profit'] >= 0 ? 'text-green-700' : 'text-red-700' }} font-bold">
                            Rp {{ number_format($data['summary']['profit'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                            <x-filament::button
                                x-data
                                x-on:click="$dispatch('open-modal', { id: 'details-{{ md5($date) }}' })"
                                color="primary"
                                size="sm">
                                Lihat Detail
                            </x-filament::button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            Tidak ada data penjualan untuk rentang waktu yang dipilih
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Details Modals --}}
        @foreach ($revenueData as $date => $data)
            <x-filament::modal
                id="details-{{ md5($date) }}"
                wire:key="{{ md5($date) }}"
                width="7xl">
                <x-slot name="header">
                    <div class="flex justify-between items-center w-full">
                        <h3 class="text-xl font-bold text-gray-900">Detail Penjualan</h3>
                        <span class="text-sm text-gray-600 font-medium">{{ $date }}</span>
                    </div>
                </x-slot>

                <div class="p-6">
                    <table class="w-full">
                        <thead class="bg-primary-100 border-b border-primary-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase tracking-wider">
                                Produk
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">
                                Kuantitas
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">
                                Pendapatan
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">
                                Biaya
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">
                                Keuntungan
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach ($data['details'] as $item)
                            <tr class="hover:bg-primary-50 transition duration-150
                                    {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item['product_name'] }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right">
                                    {{ number_format($item['quantity_sold_in_base_unit'], 2) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-green-600 font-semibold">
                                    Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-red-600">
                                    Rp {{ number_format($item['cogs'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right
                                        {{ $item['profit'] >= 0 ? 'text-green-700' : 'text-red-700' }} font-bold">
                                    Rp {{ number_format($item['profit'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::modal>
        @endforeach
    </div>
</x-filament::page>


