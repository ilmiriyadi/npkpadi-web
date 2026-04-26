<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Petani - NPK Padi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    </style>
</head>
<body class="text-gray-800 antialiased overflow-x-hidden">

    <div class="md:hidden bg-white border-b border-gray-200 fixed top-0 w-full z-40 flex items-center justify-between px-5 py-4 shadow-sm">
        <div class="flex items-center font-bold text-lg text-[#387F39]">
            <i class="fa-solid fa-leaf mr-2"></i> NPK Padi
        </div>
        <button onclick="toggleSidebar()" class="text-gray-500 hover:text-green-600 focus:outline-none text-2xl transition-colors">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 hidden transition-opacity md:hidden backdrop-blur-sm"></div>

    <aside id="sidebar" class="bg-white w-72 min-h-screen border-r border-gray-100 fixed top-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-lg md:shadow-none">
        
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <div class="text-2xl font-black text-[#387F39] flex items-center tracking-tight">
                <i class="fa-solid fa-leaf mr-2"></i> NPK Padi
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-red-500 text-2xl transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="px-6 py-6 flex items-center border-b border-gray-50">
            <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-lg mr-4 border-2 border-green-200">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate mt-0.5"><i class="fa-solid fa-tractor mr-1 text-gray-400"></i> Petani</p>
            </div>
        </div>

        <nav class="p-4 space-y-2 flex-1 overflow-y-auto custom-scrollbar">
            <a href="{{ route('farmer.dashboard') }}" class="flex items-center px-4 py-3.5 rounded-xl font-medium transition-colors {{ request()->routeIs('farmer.dashboard') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                <i class="fa-solid fa-house w-6 text-center text-lg mr-3"></i> Dashboard
            </a>
            
            <a href="{{ route('farmer.lahan') }}" class="flex items-center px-4 py-3.5 rounded-xl font-medium transition-colors {{ request()->routeIs('farmer.lahan') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                <i class="fa-solid fa-map-location-dot w-6 text-center text-lg mr-3"></i> Kelola Lahan
            </a>

            <a href="{{ route('farmer.history') }}" class="flex items-center px-4 py-3.5 rounded-xl font-medium transition-colors {{ request()->routeIs('farmer.history') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                <i class="fa-solid fa-clock-rotate-left w-6 text-center text-lg mr-3"></i> Riwayat Deteksi
            </a>
        </nav>

        <div class="p-4 border-t border-gray-50 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center text-lg mr-3"></i> Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    <main class="md:ml-72 pt-16 md:pt-0 min-h-screen flex flex-col transition-all duration-300">
        
        <header class="hidden md:flex bg-white/80 backdrop-blur-md px-8 py-5 border-b border-gray-100 justify-between items-center sticky top-0 z-30">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">@yield('header_title')</h1>
            </div>
            <div class="flex items-center space-x-4">
                <button class="w-10 h-10 rounded-full bg-gray-50 text-gray-500 hover:text-green-600 hover:bg-green-50 flex items-center justify-center transition-colors relative">
                    <i class="fa-regular fa-bell"></i>
                    <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
            </div>
        </header>

        <div class="md:hidden px-6 pt-6 pb-2">
            <h1 class="text-xl font-bold text-gray-800">@yield('header_title')</h1>
        </div>

        <div class="p-6 md:p-8 flex-1">
            
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center" role="alert">
                <i class="fa-solid fa-circle-check text-xl mr-3"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
            @endif

            @yield('content')
            
        </div>
        
        <footer class="mt-auto px-6 py-4 border-t border-gray-100 bg-white/50 text-center md:text-left text-sm text-gray-500">
            &copy; 2026 NPK Padi - Politeknik Negeri Banjarmasin.
        </footer>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Toggle class CSS
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    
    @yield('scripts')
</body>
</html>