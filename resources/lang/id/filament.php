<?php

return [
    // Navigation Groups
    'navigation' => [
        'products_inventory' => 'Produk & Inventori',
        'purchases' => 'Pembelian',
        'sales' => 'Penjualan',
    ],

    // Resource-specific translations
    'resources' => [
        // Products
        'product' => [
            'singular' => 'Produk',
            'plural' => 'Daftar Produk',
            'fields' => [
                'name' => 'Nama Produk',
                'base_unit' => 'Satuan Dasar',
                'base_unit_helper' => 'Pilih satuan dasar untuk produk ini, seperti kilogram, liter, atau unit.',
                'notes' => 'Catatan Produk',
            ],
        ],
        // Units
        'unit' => [
            'singular' => 'Unit',
            'plural' => 'Daftar Unit',
            'fields' => [
                'product_id' => 'ID Produk',
                'unit_name' => 'Nama Unit',
                'conversion_factor' => 'Faktor Konversi',
                'conversion_factor_helper' => 'Faktor untuk mengonversi unit ke satuan dasar.',
            ],
        ],
        // Inventory
        'inventory' => [
            'singular' => 'Inventori',
            'plural' => 'Daftar Inventori',
            'fields' => [
                'product_id' => 'ID Produk',
                'purchase_item_id' => 'ID Item Pembelian',
                'quantity' => 'Kuantitas',
                'unit' => 'Unit',
                'price_per_unit' => 'Harga Per Unit',
            ],
        ],

        // Suppliers
        'supplier' => [
            'singular' => 'Pemasok',
            'plural' => 'Daftar Pemasok',
            'fields' => [
                'name' => 'Nama Pemasok',
                'contact_person' => 'Kontak Person',
                'contact_person_helper' => 'Orang yang dapat dihubungi.',
                'phone' => 'Nomor HP',
                'email' => 'Alamat Email',
                'address' => 'Alamat Lengkap',
            ],
        ],
        // Purchases
        'purchase' => [
            'singular' => 'Pembelian',
            'plural' => 'Daftar Pembelian',
            'fields' => [
                'code' => 'Kode Pembelian',
                'supplier_id' => 'Pemasok',
                'date' => 'Tanggal Pembelian',
                'invoice_image' => 'Gambar Faktur',
            ],
        ],
        // Purchase Items
        'purchase_item' => [
            'singular' => 'Item Pembelian',
            'plural' => 'Daftar Item Pembelian',
            'fields' => [
                'purchase_id' => 'Pembelian',
                'product_id' => 'Produk',
                'quantity' => 'Kuantitas',
                'unit' => 'Unit',
                'price_per_unit' => 'Harga Per Unit',
                'discount' => 'Diskon',
            ],
        ],

        // Customers
        'customer' => [
            'singular' => 'Pelanggan',
            'plural' => 'Daftar Pelanggan',
            'fields' => [
                'name' => 'Nama Lengkap',
                'contact_person' => 'Kontak Person',
                'contact_person_helper' => 'Orang yang dapat dihubungi.',
                'phone' => 'Nomor HP',
                'email' => 'Alamat Email',
                'address' => 'Alamat Lengkap',
                'latitude' => 'Garis Lintang',
                'latitude_helper' => 'Masukkan koordinat untuk lokasi ini.',
                'longitude' => 'Garis Bujur',
                'longitude_helper' => 'Masukkan koordinat untuk lokasi ini.',
            ],
        ],
        // Sales
        'sales' => [
            'singular' => 'Penjualan',
            'plural' => 'Daftar Penjualan',
            'fields' => [
                'code' => 'Kode Penjualan',
                'customer_id' => 'ID Pelanggan',
                'date' => 'Tanggal Penjualan',
            ],
        ],
        // Sales Items
        'sales_item' => [
            'singular' => 'Item Penjualan',
            'plural' => 'Daftar Item Penjualan',
            'fields' => [
                'sales_id' => 'ID Penjualan',
                'product_id' => 'ID Produk',
                'quantity' => 'Kuantitas',
                'unit' => 'Unit',
                'price_per_unit' => 'Harga Per Unit',
                'discount' => 'Diskon',
            ],
        ],
    ],

    // General Translations
    'general' => [
        'actions' => [
            'create' => 'Buat',
            'edit' => 'Ubah',
            'delete' => 'Hapus',
            'view' => 'Lihat',
        ],
        'fields' => [
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Terakhir Diperbarui',
        ],
    ],
];
