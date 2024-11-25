<div class="space-y-3">
    {{-- Compact Summary Cards --}}
    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
        @php
            // Calculate all metrics once to avoid multiple iterations
            $purchaseTotal = $history->where('type', 'Purchase')->sum(fn($item) => $item->quantity * $item->price_per_unit);
            $salesTotal = $history->where('type', 'Sale')->sum(fn($item) => $item->quantity * $item->price_per_unit);
            $grossProfit = $salesTotal - $purchaseTotal;
            $profitMargin = $salesTotal > 0 ? ($grossProfit / $salesTotal) * 100 : 0;
        @endphp

        {{-- Revenue Card --}}
        <div class="rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <div class="rounded-md bg-blue-50 p-1.5 dark:bg-blue-500/10">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.resources.inventory.fields.revenue') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        IDR {{ number_format($salesTotal, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- COGS Card --}}
        <div class="rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <div class="rounded-md bg-green-50 p-1.5 dark:bg-green-500/10">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.resources.inventory.fields.cogs') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        IDR {{ number_format($purchaseTotal, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Gross Profit Card --}}
        <div class="rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <div class="rounded-md bg-{{ $grossProfit >= 0 ? 'emerald' : 'red' }}-50 p-1.5 dark:bg-{{ $grossProfit >= 0 ? 'emerald' : 'red' }}-500/10">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.resources.inventory.fields.gross_profit') }}</p>
                    <div class="flex items-baseline gap-x-1">
                        <p class="text-sm font-semibold {{ $grossProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            IDR {{ number_format($grossProfit, 0, ',', '.') }}
                        </p>
                        <span class="text-xs text-gray-500">({{ number_format($profitMargin, 1, ',', '.') }}%)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction Count Card --}}
        <div class="rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <div class="rounded-md bg-gray-50 p-1.5 dark:bg-gray-800">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.resources.inventory.fields.transaction_count') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($history->count(), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Transaction History Table (Same as before) --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="overflow-x-auto">
            <div class="max-h-[400px] overflow-y-auto">
                <table class="w-full divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-start text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.inventory.fields.date') }}</th>
                        <th class="px-4 py-3 text-start text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.inventory.fields.type') }}</th>
                        <th class="px-4 py-3 text-end text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.inventory.fields.quantity') }}</th>
                        <th class="px-4 py-3 text-start text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.inventory.fields.unit') }}</th>
                        <th class="px-4 py-3 text-end text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.inventory.fields.price_per_unit') }}</th>
                        <th class="px-4 py-3 text-end text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.inventory.fields.total') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    @forelse ($history as $item)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                {{ $item->type }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-end">
                                {{ number_format($item->quantity, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $item->unit->unit_name ?? 'Tidak Ada' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-end">
                                Rp {{ number_format($item->price_per_unit, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium {{ $item->type === 'Purchase' ? 'text-custom-600 dark:text-custom-500' : 'text-info-600 dark:text-info-500'}} text-end">
                                Rp {{ number_format($item->quantity * $item->price_per_unit, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center">
                                <div class="flex flex-col items-center justify-center gap-y-2">
                                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-800">
                                        <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('filament.general.messages.no_items.title') }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('filament.general.messages.no_items.description') }}</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
