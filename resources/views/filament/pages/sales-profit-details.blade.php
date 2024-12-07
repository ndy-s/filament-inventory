<x-filament::page>
    <div class="space-y-8">
        <x-filament::card>
            <h3 class="text-lg font-bold text-primary-600 mb-4">Riwayat Penjualan</h3>
            <div class="overflow-x-auto">
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
                            Harga Pokok
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">
                            Laba Kotor
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-primary-700 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse ($this->revenueData as $date => $data)
                        <tr class="hover:bg-primary-50 transition duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-4 text-sm text-gray-900">{{ $date }}</td>
                            <td class="px-4 py-4 text-sm text-right text-green-600 font-semibold">
                                Rp {{ number_format($data['summary']['revenue'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-right text-red-600">
                                Rp {{ number_format($data['summary']['cogs'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-right {{ $data['summary']['profit'] >= 0 ? 'text-green-700' : 'text-red-700' }} font-bold">
                                Rp {{ number_format($data['summary']['profit'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-center">
                                <x-filament::button
                                    x-data
                                    x-on:click="$dispatch('open-modal', { id: 'details-{{ md5($date) }}' })"
                                    color="primary"
                                    size="sm">
                                    Details
                                </x-filament::button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                No sales data available
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::card>

        {{-- Modal Detail --}}
        @foreach ($this->revenueData as $date => $data)
            <x-filament::modal id="details-{{ md5($date) }}" wire:key="{{ md5($date) }}" width="7xl">
                <x-slot name="header">
                    <h3 class="text-xl font-bold">Detail Penjualan {{ $date }}</h3>
                </x-slot>
                <div class="p-6">
                    <table class="w-full">
                        <thead class="bg-primary-100 border-b border-primary-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase tracking-wider">Produk</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">Kuantitas</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">Pendapatan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">Harga Pokok</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase tracking-wider">Laba Kotor</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data['details'] as $item)
                            <tr class="hover:bg-primary-50">
                                <td class="px-4 py-4 text-sm">{{ $item['product_name'] }}</td>
                                <td class="px-4 py-4 text-sm text-right">{{ $item['quantity_sold_in_base_unit'] }}</td>
                                <td class="px-4 py-4 text-sm text-right text-green-600">
                                    Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-right text-red-600">
                                    Rp {{ number_format($item['cogs'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-right {{ $item['profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
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
