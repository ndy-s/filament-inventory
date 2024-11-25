<div class="space-y-3">
    {{-- sales Summary Cards --}}
    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total Amount Card --}}
        <div class="rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <div class="rounded-md bg-blue-50 p-1.5 dark:bg-blue-500/10">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.875 14.25l1.214 1.942a2.25 2.25 0 001.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 011.872 1.002l.164.246a2.25 2.25 0 001.872 1.002h2.092a2.25 2.25 0 001.872-1.002l.164-.246A2.25 2.25 0 0116.954 9h4.636M2.41 9a2.25 2.25 0 00-.16.832V12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 01.382-.632l3.285-3.832a2.25 2.25 0 011.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0021.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.resources.sales.fields.code') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $sales->code ?? __('filament.general.fields.na') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Items Card --}}
        <div class="rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <div class="rounded-md bg-green-50 p-1.5 dark:bg-green-500/10">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.resources.sales.fields.customer') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $sales->customer->name ?? __('filament.general.fields.na')  }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Average Price Card --}}
        <div class="rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <div class="rounded-md bg-emerald-50 p-1.5 dark:bg-emerald-500/10">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.resources.sales.fields.date') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $sales->date ? $sales->date->format('d M Y') : __('filament.general.fields.na') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Quantity Card --}}
        <div class="rounded-lg bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <div class="rounded-md bg-gray-50 p-1.5 dark:bg-gray-800">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.resources.sales.fields.total') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        IDR {{ number_format($salesItems->sum(fn($item) => ($item->quantity * $item->price_per_unit) - ($item->discount ?? 0)), 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Items Table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="overflow-x-auto">
            <div class="max-h-[400px] overflow-y-auto">
                <table class="w-full divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2.5 text-start text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.sales_item.fields.sales') }}</th>
                        <th class="px-4 py-2.5 text-end text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.sales_item.fields.quantity') }}</th>
                        <th class="px-4 py-2.5 text-end text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.sales_item.fields.unit') }}</th>
                        <th class="px-4 py-2.5 text-end text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.sales_item.fields.price_per_unit') }}</th>
                        <th class="px-4 py-2.5 text-end text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.sales_item.fields.discount') }}</th>
                        <th class="px-4 py-2.5 text-end text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('filament.resources.sales_item.fields.total') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    @forelse ($salesItems as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="whitespace-nowrap px-4 py-2.5 text-sm text-gray-900 dark:text-white">
                                {{ $item->product->name ?? __('N/A') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 text-end">
                                {{ number_format($item->quantity ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 text-end">
                                {{ $item->unit->unit_name ?? __('N/A') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 text-end">
                                Rp {{ number_format($item->price_per_unit ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 text-end">
                                Rp {{ number_format($item->discount ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-sm font-medium text-custom-600 dark:text-custom-500 text-end">
                                Rp {{ number_format(($item->quantity * $item->price_per_unit) - ($item->discount ?? 0), 2, ',', '.') }}
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
