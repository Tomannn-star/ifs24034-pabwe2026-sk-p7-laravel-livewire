<div>
    <!-- Toast UI Editor CDN Styles & Scripts -->
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center space-x-3">
            <a href="{{ route('stocks.index') }}" class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition" title="Kembali ke Daftar Stok">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center space-x-2.5">
                    <h1 class="text-2xl font-bold text-slate-900 truncate max-w-md sm:max-w-xl">{{ $stock->name }}</h1>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $stock->status instanceof \App\Features\Stock\Enums\StockStatus ? $stock->status->badgeClass() : 'bg-slate-100 text-slate-700' }}">
                        {{ $stock->status instanceof \App\Features\Stock\Enums\StockStatus ? $stock->status->label() : ucfirst((string) $stock->status) }}
                    </span>
                    @if ($stock->sku)
                        <span class="px-2 py-0.5 text-xs font-mono bg-slate-100 text-slate-600 rounded">
                            {{ $stock->sku }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Dibuat pada {{ $stock->created_at ? $stock->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Increment / Decrement Quantity -->
            <div class="flex items-center space-x-1 bg-slate-100 border border-slate-200 rounded-lg p-1">
                <button wire:click="decrementQuantity" wire:loading.attr="disabled" class="px-2.5 py-1 bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-600 rounded text-xs font-bold transition shadow-xs" title="Kurangi 1">
                    - 1
                </button>
                <span class="px-2 font-bold text-sm text-slate-800">{{ $stock->quantity }} {{ $stock->unit ?? 'pcs' }}</span>
                <button wire:click="incrementQuantity" wire:loading.attr="disabled" class="px-2.5 py-1 bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-600 rounded text-xs font-bold transition shadow-xs" title="Tambah 1">
                    + 1
                </button>
            </div>

            <!-- Edit Stock Button -->
            <button wire:click="openEditModal" wire:loading.attr="disabled" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-75 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Barang</span>
            </button>

            <!-- Delete Button -->
            <button wire:click="confirmDeleteStock" wire:loading.attr="disabled" class="inline-flex items-center space-x-1 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold rounded-lg transition disabled:opacity-75">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Hapus</span>
            </button>
        </div>
    </div>

    <!-- Cover Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <input type="file" wire:model="coverPhoto" id="coverPhotoInput" accept="image/*" class="hidden">

        @if ($stock->cover_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($stock->cover_image_path))
            <div class="relative group h-64 sm:h-80 w-full bg-slate-900 overflow-hidden">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($stock->cover_image_path) }}?v={{ time() }}" alt="Cover Barang" class="w-full h-full object-cover">
                
                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-3">
                    <button type="button" onclick="document.getElementById('coverPhotoInput').click()" class="px-4 py-2 bg-white text-slate-800 text-xs font-semibold rounded-xl shadow-lg hover:bg-slate-50 transition flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Ganti Cover</span>
                    </button>
                    <button type="button" wire:click="confirmDeleteCover" wire:loading.attr="disabled" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-xl shadow-lg hover:bg-rose-700 disabled:opacity-75 transition flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus Cover</span>
                    </button>
                </div>
            </div>
        @else
            <div class="p-8 text-center bg-gradient-to-b from-slate-50 to-white flex flex-col items-center justify-center border-b border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 mb-3 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Tambahkan Gambar Produk / Barang</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm">Mendukung format gambar PNG, JPG, GIF hingga ukuran 5MB untuk katalog barang.</p>
                
                <button type="button" onclick="document.getElementById('coverPhotoInput').click()" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Upload Cover Gambar</span>
                </button>
            </div>
        @endif

        <div wire:loading wire:target="coverPhoto" class="p-3 bg-blue-50 border-t border-blue-100 text-center text-xs text-blue-700 font-semibold animate-pulse">
            Sedang mengunggah gambar cover...
        </div>
        @error('coverPhoto')
            <div class="p-3 bg-rose-50 border-t border-rose-100 text-center text-xs text-rose-600 font-medium">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Metadata Overview Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Jumlah Stok</span>
            <div class="text-2xl font-black text-slate-900">
                {{ $stock->quantity }} <span class="text-sm font-medium text-slate-500">{{ $stock->unit ?? 'pcs' }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Ambang Min. Stok</span>
            <div class="text-2xl font-black text-slate-900">
                {{ $stock->min_stock }} <span class="text-sm font-medium text-slate-500">{{ $stock->unit ?? 'pcs' }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Harga Satuan</span>
            <div class="text-xl font-bold text-emerald-700 mt-1">
                {{ $stock->price !== null ? 'Rp ' . number_format((float) $stock->price, 0, ',', '.') : '-' }}
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Status Ketersediaan</span>
            <div class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $stock->status instanceof \App\Features\Stock\Enums\StockStatus ? $stock->status->badgeClass() : 'bg-slate-100 text-slate-700' }}">
                    {{ $stock->status instanceof \App\Features\Stock\Enums\StockStatus ? $stock->status->label() : ucfirst((string) $stock->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Description Viewer Section -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 mb-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Deskripsi & Spesifikasi Barang</h2>
                <p class="text-xs text-slate-500 mt-0.5">Diformat dengan Markdown</p>
            </div>
            <button wire:click="openEditModal" class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 hover:bg-blue-50 border border-blue-200 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Ubah Deskripsi</span>
            </button>
        </div>

        @if (!empty($stock->description))
            <div wire:ignore
                 x-data="{
                     init() {
                         if (window.toastui && toastui.Editor) {
                             toastui.Editor.factory({
                                 el: this.$refs.viewerContainer,
                                 viewer: true,
                                 initialValue: @js($stock->description)
                             });
                         }
                     }
                 }"
                 class="prose prose-slate max-w-none text-slate-800">
                <div x-ref="viewerContainer"></div>
            </div>
        @else
            <div class="py-12 text-center text-slate-400">
                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm font-medium text-slate-600">Belum ada deskripsi untuk barang ini</p>
                <button wire:click="openEditModal" class="mt-4 px-3.5 py-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                    + Tambahkan Deskripsi Sekarang
                </button>
            </div>
        @endif
    </div>

    <!-- Edit Stock Modal -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 my-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-lg font-bold text-slate-900">Ubah Data Barang</h3>
                    <button type="button" wire:click="closeEditModal" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveStock" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                        <input wire:model="name" type="text"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">SKU</label>
                            <input wire:model="sku" type="text"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('sku') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Satuan <span class="text-rose-500">*</span></label>
                            <input wire:model="unit" type="text"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('unit') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah <span class="text-rose-500">*</span></label>
                            <input wire:model="quantity" type="number" min="0"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('quantity') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Min. Stok <span class="text-rose-500">*</span></label>
                            <input wire:model="minStock" type="number" min="0"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('minStock') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                            <input wire:model="price" type="number" min="0" step="any"
                                class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('price') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status Ketersediaan <span class="text-rose-500">*</span></label>
                        <select wire:model="status"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            @foreach ($statuses as $st)
                                <option value="{{ $st->value }}">{{ $st->label() }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi / Spesifikasi</label>
                        <textarea wire:model="description" rows="4"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('description') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeEditModal"
                            class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($confirmingStockDeletion)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h4 class="text-center font-bold text-slate-900 text-base mb-1">Hapus Barang Ini?</h4>
                <p class="text-center text-sm text-slate-500 mb-6">Barang ini akan dihapus secara permanen beserta foto cover yang terkait.</p>
                <div class="flex space-x-3">
                    <button type="button" wire:click="cancelDeleteStock"
                        class="flex-1 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">
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

    <!-- Delete Cover Confirmation Modal -->
    @if ($confirmingCoverDeletion)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200">
                <h4 class="text-center font-bold text-slate-900 text-base mb-1">Hapus Cover Barang?</h4>
                <p class="text-center text-sm text-slate-500 mb-6">Gambar cover akan dihapus secara permanen.</p>
                <div class="flex space-x-3">
                    <button type="button" wire:click="cancelDeleteCover"
                        class="flex-1 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="button" wire:click="deleteCoverPhoto"
                        class="flex-1 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-sm font-semibold text-white shadow-sm">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

