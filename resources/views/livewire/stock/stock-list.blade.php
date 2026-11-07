<div>
    <!-- Toast UI Editor CDN Styles & Scripts -->
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>

    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Daftar Stok Barang Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola stok, harga, dan kuantitas inventaris barang secara teratur</p>
        </div>

        <div class="relative" x-data="{ open: false, loading: null }" @click.outside="open = false" @keydown.escape.window="open = false">
            <button type="button" @click="open = !open" :disabled="!!loading"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 shadow-sm transition disabled:opacity-60"
                title="Menu aksi">
                <svg x-show="!loading" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
                <svg x-show="loading" x-cloak class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </button>

            <div x-show="open" x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 z-30 mt-2 w-56 origin-top-right rounded-xl border border-slate-200 bg-white py-1.5 shadow-xl shadow-slate-200/70">
                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-600">Aksi Cepat</div>

                <button type="button" :disabled="!!loading"
                    @click="
                        loading = 'create'; open = false;
                        $wire.openCreateModal().finally(() => loading = null);
                    "
                    class="flex w-full items-center gap-2.5 px-3 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition disabled:opacity-60">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg x-show="loading !== 'create'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <svg x-show="loading === 'create'" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </span>
                    Tambah Barang
                </button>

                <button type="button" :disabled="!!loading"
                    @click="
                        loading = 'ai'; open = false;
                        $wire.openAiModal().finally(() => loading = null);
                    "
                    class="flex w-full items-center gap-2.5 px-3 py-2.5 text-sm font-semibold text-purple-700 hover:bg-purple-50 transition disabled:opacity-60">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                        <svg x-show="loading !== 'ai'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        <svg x-show="loading === 'ai'" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </span>
                    Generate AI Stok
                </button>

                <div class="my-1.5 border-t border-slate-100"></div>
                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-600">Spreadsheet</div>

                <button type="button" :disabled="!!loading"
                    @click="
                        loading = 'import'; open = false;
                        $wire.openImportModal().finally(() => loading = null);
                    "
                    class="flex w-full items-center gap-2.5 px-3 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-50 transition disabled:opacity-60">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                        <svg x-show="loading !== 'import'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        <svg x-show="loading === 'import'" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </span>
                    Import Excel
                </button>

                <button type="button" :disabled="!!loading"
                    @click="
                        loading = 'export'; open = false;
                        $wire.exportExcel().finally(() => loading = null);
                    "
                    class="flex w-full items-center gap-2.5 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition disabled:opacity-60">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                        <svg x-show="loading !== 'export'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        <svg x-show="loading === 'export'" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </span>
                    <span x-text="loading === 'export' ? 'Mengekspor...' : 'Export Excel'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="w-full sm:w-72 relative">
            <input wire:model.live.debounce.300ms="search" type="text" id="searchStockInput" aria-label="Cari nama barang, SKU, atau deskripsi" placeholder="Cari nama barang, SKU, atau deskripsi..."
                class="w-full pl-3.5 pr-9 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div wire:loading wire:target="search,statusFilter" class="absolute right-3 top-2.5">
                <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </div>

        <div class="flex items-center space-x-3 w-full sm:w-auto">
            <label for="statusFilter" class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Status:</label>
            <select id="statusFilter" aria-label="Filter Status Stok" wire:model.live="statusFilter"
                class="px-3 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Stock List Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        @if ($stocks->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="text-base font-semibold text-slate-700">Belum ada stok barang ditemukan</p>
                <p class="text-sm text-slate-500 mt-1">Tambahkan barang baru atau gunakan AI Generator</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach ($stocks as $stock)
                    <li wire:key="stock-{{ $stock->id }}" class="p-4 sm:p-5 hover:bg-slate-50/75 transition flex items-start justify-between gap-4 first:rounded-t-2xl last:rounded-b-2xl">
                        <div class="flex items-start space-x-3.5 flex-1 min-w-0">
                            <!-- Quantity Adjuster (+/-) -->
                            <div class="flex flex-col items-center bg-slate-100 rounded-lg p-1 border border-slate-200 flex-shrink-0">
                                <button type="button" wire:click="incrementQuantity({{ $stock->id }})" title="Tambah stok"
                                    class="w-7 h-6 flex items-center justify-center text-slate-600 hover:text-blue-600 hover:bg-white rounded transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                                <span class="font-bold text-sm text-slate-800 px-1.5 py-0.5">
                                    {{ $stock->quantity }}
                                </span>
                                <button type="button" wire:click="decrementQuantity({{ $stock->id }})" title="Kurangi stok"
                                    class="w-7 h-6 flex items-center justify-center text-slate-600 hover:text-rose-600 hover:bg-white rounded transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                </button>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('stocks.show', $stock->id) }}"
                                        class="text-base font-semibold text-slate-900 hover:text-blue-600 transition">
                                        {{ $stock->name }}
                                    </a>

                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full border {{ $stock->status instanceof \App\Features\Stock\Enums\StockStatus ? $stock->status->badgeClass() : 'bg-slate-100 text-slate-700' }}">
                                        {{ $stock->status instanceof \App\Features\Stock\Enums\StockStatus ? $stock->status->label() : ucfirst((string) $stock->status) }}
                                    </span>

                                    @if ($stock->sku)
                                        <span class="px-2 py-0.5 text-xs font-mono bg-slate-100 text-slate-600 rounded">
                                            {{ $stock->sku }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                    <span>Satuan: <strong class="text-slate-700">{{ $stock->unit ?? 'pcs' }}</strong></span>
                                    <span>Min Stok: <strong class="text-slate-700">{{ $stock->min_stock }}</strong></span>
                                    @if ($stock->price !== null)
                                        <span>Harga: <strong class="text-emerald-700">Rp {{ number_format((float) $stock->price, 0, ',', '.') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Row Actions Menu -->
                        <div class="relative flex-shrink-0" x-data="{ open: false, loading: null }" @click.outside="open = false" @keydown.escape.window="open = false">
                            <button type="button" @click="open = !open" :disabled="!!loading"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-transparent text-slate-500 hover:border-slate-200 hover:bg-white hover:text-slate-800 transition disabled:opacity-60"
                                title="Tindakan">
                                <svg x-show="!loading" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                                <svg x-show="loading" x-cloak class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </button>

                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-1.5 w-48 origin-top-right rounded-xl border border-slate-200 bg-white py-1.5 shadow-xl shadow-slate-200/70">
                                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Tindakan</div>

                                <a href="{{ route('stocks.show', $stock->id) }}" @click="open = false"
                                    class="flex w-full items-center gap-2.5 px-3 py-2 text-sm font-semibold text-sky-700 hover:bg-sky-50 transition">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </span>
                                    Lihat Detail
                                </a>

                                <button type="button" :disabled="!!loading"
                                    @click="
                                        loading = 'edit'; open = false;
                                        $wire.openEditModal({{ $stock->id }}).finally(() => loading = null);
                                    "
                                    class="flex w-full items-center gap-2.5 px-3 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 transition disabled:opacity-60">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                        <svg x-show="loading !== 'edit'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <svg x-show="loading === 'edit'" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </span>
                                    <span x-text="loading === 'edit' ? 'Membuka...' : 'Edit Barang'"></span>
                                </button>

                                <div class="my-1 border-t border-slate-100"></div>

                                <button type="button" :disabled="!!loading"
                                    @click="
                                        loading = 'delete'; open = false;
                                        $wire.confirmDelete({{ $stock->id }});
                                        loading = null;
                                    "
                                    class="flex w-full items-center gap-2.5 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50 transition disabled:opacity-60">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </span>
                                    Hapus Barang
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="p-4 border-t border-slate-100">
                {{ $stocks->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Stock Modal -->
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 my-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $editingStockId ? 'Edit Stok Barang' : 'Tambah Barang Baru' }}
                    </h3>
                    <button type="button" wire:click="closeFormModal" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveStock" class="space-y-4">
                    <div>
                        <label for="formName" class="block text-sm font-medium text-slate-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                        <input wire:model="formName" type="text" id="formName" aria-label="Nama Barang" placeholder="Contoh: Kertas HVS A4 80gr"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('formName') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="formSku" class="block text-sm font-medium text-slate-700 mb-1">SKU / Kode</label>
                            <input wire:model="formSku" type="text" id="formSku" aria-label="SKU atau Kode" placeholder="Contoh: ATK-001"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('formSku') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="formUnit" class="block text-sm font-medium text-slate-700 mb-1">Satuan <span class="text-rose-500">*</span></label>
                            <input wire:model="formUnit" type="text" id="formUnit" aria-label="Satuan" placeholder="pcs, rim, box, botol"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('formUnit') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label for="formQuantity" class="block text-sm font-medium text-slate-700 mb-1">Jumlah <span class="text-rose-500">*</span></label>
                            <input wire:model="formQuantity" type="number" min="0" id="formQuantity" aria-label="Jumlah"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('formQuantity') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="formMinStock" class="block text-sm font-medium text-slate-700 mb-1">Min. Stok <span class="text-rose-500">*</span></label>
                            <input wire:model="formMinStock" type="number" min="0" id="formMinStock" aria-label="Min. Stok"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('formMinStock') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="formPrice" class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                            <input wire:model="formPrice" type="number" min="0" step="any" placeholder="0" id="formPrice" aria-label="Harga (Rp)"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('formPrice') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="formStatus" class="block text-sm font-medium text-slate-700 mb-1">Status Stok <span class="text-rose-500">*</span></label>
                        <select wire:model="formStatus" id="formStatus" aria-label="Status Stok"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            @foreach ($statuses as $st)
                                <option value="{{ $st->value }}">{{ $st->label() }}</option>
                            @endforeach
                        </select>
                        @error('formStatus') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="formDescription" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi / Spesifikasi</label>
                        <textarea wire:model="formDescription" id="formDescription" aria-label="Deskripsi atau Spesifikasi" rows="3" placeholder="Deskripsi barang..."
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('formDescription') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeFormModal"
                            class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition">
                            Simpan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($confirmingStockDeletionId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-center font-bold text-slate-900 text-base mb-1">Hapus Barang?</h3>
                <p class="text-center text-sm text-slate-600 mb-6">Barang ini akan dihapus secara permanen dari sistem inventaris.</p>
                <div class="flex space-x-3">
                    <button type="button" wire:click="cancelDelete"
                        class="flex-1 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="button" wire:click="deleteStock"
                        class="flex-1 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-sm font-semibold text-white shadow-sm">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Excel Modal -->
    @if ($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-lg font-bold text-slate-900">Import Stok dari Excel</h3>
                    <button type="button" wire:click="closeImportModal" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="importExcel" class="space-y-4">
                    <div>
                        <label for="importFile" class="block text-sm font-medium text-slate-700 mb-2">Pilih File Spreadsheet (.xlsx, .xls, .csv)</label>
                        <input wire:model="importFile" type="file" id="importFile" aria-label="Pilih File Spreadsheet" accept=".xlsx,.xls,.csv"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('importFile') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeImportModal"
                            class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-75 text-white font-semibold rounded-lg shadow-sm transition">
                            <span wire:loading.remove wire:target="importExcel">Mulai Import</span>
                            <span wire:loading wire:target="importExcel">Mengimpor...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- AI Generator Modal -->
    @if ($showAiModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 my-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 bg-purple-100 text-purple-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <h3 class="text-lg font-bold text-slate-900">Generate Stok dengan AI</h3>
                    </div>
                    <button type="button" wire:click="closeAiModal" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="aiPrompt" class="block text-sm font-medium text-slate-700 mb-1">Permintaan / Prompt</label>
                        <textarea wire:model="aiPrompt" id="aiPrompt" aria-label="Permintaan Prompt AI" rows="3" placeholder="Contoh: Buatkan 5 stok perlengkapan alat tulis kantor beserta estimasi harga dan kuantitas"
                            class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                        @error('aiPrompt') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <label for="aiCount" class="text-sm font-medium text-slate-700">Jumlah item:</label>
                            <input wire:model="aiCount" id="aiCount" aria-label="Jumlah item" type="number" min="1" max="10" class="w-16 px-2.5 py-1.5 text-sm rounded-lg border border-slate-300">
                        </div>

                        <button type="button" wire:click="generateWithAi" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:opacity-75 text-white font-semibold rounded-lg text-sm shadow-sm transition flex items-center space-x-2">
                            <svg wire:loading wire:target="generateWithAi" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="generateWithAi">Generate Sekarang</span>
                            <span wire:loading wire:target="generateWithAi">Memproses AI...</span>
                        </button>
                    </div>

                    @if ($aiErrorMessage)
                        <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700">
                            {{ $aiErrorMessage }}
                        </div>
                    @endif

                    @if (!empty($aiGeneratedItems))
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <h4 class="text-sm font-bold text-slate-800 mb-2">Pilih Item untuk Disimpan:</h4>
                            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                @foreach ($aiGeneratedItems as $index => $item)
                                    <label class="flex items-start space-x-3 p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                                        <input type="checkbox" wire:model="aiSelectedIndices" value="{{ $index }}" class="mt-1 rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                                        <div class="flex-1 text-xs">
                                            <p class="font-bold text-slate-900">{{ $item['name'] }} <span class="font-normal text-slate-500">({{ $item['quantity'] }} {{ $item['unit'] }})</span></p>
                                            @if (!empty($item['sku']))
                                                <span class="text-slate-400 font-mono text-[10px]">SKU: {{ $item['sku'] }}</span>
                                            @endif
                                            @if (!empty($item['description']))
                                                <p class="text-slate-500 mt-0.5">{{ $item['description'] }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-end space-x-3 mt-4 pt-3 border-t border-slate-100">
                                <button type="button" wire:click="closeAiModal" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                                    Batal
                                </button>
                                <button type="button" wire:click="saveAiGenerated"
                                    class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg text-sm shadow-sm transition">
                                    Simpan Item Terpilih
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

