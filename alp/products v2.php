<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IndustrialHub - Product Catalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .nav-active {
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 2px;
            font-weight: 600;
        }
        .map-placeholder {
            background: linear-gradient(135deg, #c8d6e5 0%, #a8bfd4 40%, #8fafc8 100%);
            position: relative;
            overflow: hidden;
        }
        .map-placeholder::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                    repeating-linear-gradient(0deg, transparent, transparent 30px, rgba(255,255,255,0.15) 30px, rgba(255,255,255,0.15) 31px),
                    repeating-linear-gradient(90deg, transparent, transparent 30px, rgba(255,255,255,0.15) 30px, rgba(255,255,255,0.15) 31px);
        }
        .map-placeholder::after {
            content: '';
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 220px;
            height: 80px;
            background: rgba(110,140,170,0.6);
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow: 40px -10px 0 rgba(90,120,150,0.5), -40px 5px 0 rgba(130,160,190,0.5);
        }
        .map-pin {
            position: absolute;
            top: 38%;
            left: 55%;
            transform: translate(-50%,-50%);
            z-index: 10;
        }
        .map-pin-dot {
            width: 14px; height: 14px;
            background: #1e3a5f;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 0 0 4px rgba(30,58,95,0.25);
        }
        .map-label {
            position: absolute;
            top: 42%;
            left: 52%;
            background: rgba(255,255,255,0.92);
            font-size: 12px;
            font-weight: 600;
            color: #1e3a5f;
            padding: 3px 10px;
            border-radius: 4px;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }
    </style>
/head>
<body class="bg-white text-gray-800 antialiased">

<!-- ═══ NAVBAR ═══ -->
<nav class="border-b border-gray-200 bg-white sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-14">
        <!-- Logo -->
        <a href="#" class="text-[#1e3a5f] font-bold text-xl tracking-tight">IndustrialHub</a>

        <!-- Nav links -->
        <div class="hidden md:flex items-center gap-8 text-sm text-gray-600 font-medium">
            <a href="#" class="text-[#1e3a5f] nav-active">Products</a>
            <a href="#" class="hover:text-[#1e3a5f] transition-colors">Industries</a>
            <a href="#" class="hover:text-[#1e3a5f] transition-colors">Contacts</a>
        </div>

        <!-- Right actions -->
        <div class="flex items-center gap-3">
            <button id="darkToggle" class="text-gray-600 hover:text-[#1e3a5f] transition-colors p-1" title="Toggle Dark Mode">
                <span class="material-symbols-outlined text-[20px]">dark_mode</span>
            </button>
            <!-- Search -->
            <div class="hidden md:flex items-center gap-2 border border-gray-300 rounded-md px-3 py-1.5 text-sm text-gray-400 bg-white w-52">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                </svg>
                <span>Search 2.4M SKUs…</span>
            </div>
            <button class="border border-gray-300 text-sm font-medium px-4 py-1.5 rounded-md text-gray-700 hover:bg-gray-50 transition">Login</button>
            <button class="bg-[#1e3a5f] text-white text-sm font-medium px-4 py-1.5 rounded-md flex items-center gap-2 hover:bg-[#162d4a] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                Cart
            </button>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="bg-gradient-to-b from-slate-50 to-white border-b border-gray-200 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Product Catalog</h1>
        <p class="text-gray-600 mb-8">Browse our comprehensive range of industrial machinery and components</p>

        <!-- Search Bar -->
        <div class="relative mb-4">
            <input
                type="text"
                placeholder="Search by SKU, part name, or description..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
            >
            <svg class="absolute right-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- SIDEBAR FILTERS -->
        <aside class="hidden md:block">
            <div class="bg-white rounded-lg p-6 border border-gray-200 sticky top-24">
                <!-- Filters Header -->
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
                    <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1H3z" clip-rule="evenodd"></path>
                        <path fill-rule="evenodd" d="M3 7a1 1 0 011-1h5a1 1 0 011 1v8a1 1 0 11-2 0V7H4a1 1 0 01-1-1zm9 0a1 1 0 011-1h2a1 1 0 011 1v8a1 1 0 11-2 0V7h-1a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="filter-title">Filters</h3>
                </div>

                <!-- Categories -->
                <div class="filter-section">
                    <h4 class="filter-title">Categories</h4>
                    <div class="space-y-2">
                        <div class="filter-item">All</div>
                        <div class="filter-item">Machines</div>
                        <div class="filter-item">Machine Parts</div>
                        <div class="filter-item">Spare Parts</div>
                    </div>
                </div>

                <!-- Stock Availability -->
                <div class="filter-section">
                    <h4 class="filter-title">Stock Availability</h4>
                    <div class="space-y-2">
                        <div class="filter-item">All</div>
                        <div class="filter-item">In Stock</div>
                        <div class="filter-item">Low Stock</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- PRODUCT GRID -->
        <div class="md:col-span-3">
            <!-- Products Count -->
            <div class="mb-6 flex items-center justify-between">
                <span class="text-sm text-gray-600 font-medium">Showing 9 products</span>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Product 1 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [Industrial Bearing Image]
                        </div>
                        <span class="badge badge-machine-parts absolute top-3 left-3">Machine Parts</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">Industrial Bearing 6205</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: BRG-6205-2RS</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$24.99</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-high font-semibold">450 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [Hydraulic Pump Image]
                        </div>
                        <span class="badge badge-machines absolute top-3 left-3">Machines</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">Hydraulic Pump HP2000</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: HYD-HP2000</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$1249.00</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-medium font-semibold">28 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [Motor Coupling Image]
                        </div>
                        <span class="badge badge-spare-parts absolute top-3 left-3">Spare Parts</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">Motor Coupling MC-50</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: CPL-MC50</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$89.50</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-high font-semibold">320 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Product 4 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [Gearbox Image]
                        </div>
                        <span class="badge badge-machines absolute top-3 left-3">Machines</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">Industrial Gearbox GB-500</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: GBX-500</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$2899.00</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-low font-semibold">15 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Product 5 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [V-Belt Image]
                        </div>
                        <span class="badge badge-spare-parts absolute top-3 left-3">Spare Parts</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">V-Belt Set VB-A42</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: BLT-VBA42</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$15.99</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-high font-semibold">780 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Product 6 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [Pneumatic Cylinder Image]
                        </div>
                        <span class="badge badge-machine-parts absolute top-3 left-3">Machine Parts</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">Pneumatic Cylinder PC-200</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: PNE-PC200</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$189.00</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-high font-semibold">95 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Product 7 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [Electric Motor Image]
                        </div>
                        <span class="badge badge-machines absolute top-3 left-3">Machines</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">Electric Motor EM-7.5KW</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: MTR-EM75</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$875.00</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-low font-semibold">42 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Product 8 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [Oil Seal Kit Image]
                        </div>
                        <span class="badge badge-spare-parts absolute top-3 left-3">Spare Parts</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">Oil Seal Kit OSK-25</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: SEL-OSK25</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$12.50</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-high font-semibold">520 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Product 9 -->
                <div class="product-card bg-white rounded-lg overflow-hidden">
                    <div class="relative">
                        <div class="product-image">
                            [Chain Sprocket Image]
                        </div>
                        <span class="badge badge-machine-parts absolute top-3 left-3">Machine Parts</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">Chain Sprocket CS-40</h3>
                        <p class="text-xs text-gray-500 mb-3 font-mono">SKU: CHN-CS40</p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-gray-900">$45.00</span>
                        </div>
                        <div class="text-xs text-gray-600 mb-4">
                            <span>Stock: </span>
                            <span class="stock-high font-semibold">210 units</span>
                        </div>
                        <button class="btn-add-cart w-full text-white py-2 rounded-lg text-sm font-semibold flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM5 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ FOOTER ═══ -->
<footer class="bg-[#1a1a2e] text-white mt-16">
    <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <p class="font-bold text-lg">IndustrialHub</p>
            <p class="text-gray-400 text-sm mt-1">Engineering-grade reliability.</p>
        </div>
        <nav class="flex flex-wrap gap-6 text-sm text-gray-400">
            <a href="#" class="hover:text-white transition">Compliance</a>
            <a href="#" class="hover:text-white transition">Terms of Service</a>
            <a href="#" class="hover:text-white transition">Privacy Policy</a>
            <a href="#" class="hover:text-white transition">Technical Support</a>
        </nav>
        <p class="text-sm text-gray-500 whitespace-nowrap">© 2024 IndustrialHub. All rights reserved.</p>
    </div>
</footer>
<button id="backToTop" aria-label="Back to top">
  <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
</button>
<script src="main.js"></script>
</body>
</html>
