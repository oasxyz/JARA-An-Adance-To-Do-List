<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Jara - Advance To-do List' }}</title>
    <!-- Tailwind CSS CDN untuk kepastian render instan dan mulus saat praktikum -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        secondary: '#06b6d4',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col font-sans">

    <!-- Top Navigation Bar -->
    <header class="bg-indigo-700 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-xl font-black tracking-wider text-white hover:text-indigo-100 transition">
                        <span class="p-1.5 bg-white text-indigo-700 rounded-lg font-bold text-sm shadow">✓</span>
                        <span>JARA <span class="text-xs font-normal bg-indigo-500 text-indigo-100 px-2 py-0.5 rounded-full">Advance To-Do</span></span>
                    </a>
                </div>

                @auth
                <!-- Quick User Switcher untuk Demo Praktikum -->
                <div class="hidden md:flex items-center space-x-2 bg-indigo-800/80 px-3 py-1.5 rounded-full border border-indigo-500/50 text-xs">
                    <span class="text-indigo-200 font-medium">⚡ Demo Switcher:</span>
                    @php
                        $allUsers = \App\Models\User::all();
                    @endphp
                    @foreach($allUsers as $u)
                        <a href="{{ route('auth.switch', $u->id) }}" 
                           class="px-2.5 py-1 rounded-full transition {{ Auth::id() === $u->id ? 'bg-amber-400 text-indigo-950 font-bold shadow-sm' : 'text-white hover:bg-indigo-600' }}">
                            {{ explode(' ', $u->name)[0] }}
                        </a>
                    @endforeach
                </div>

                <!-- User Info & Logout -->
                <div class="flex items-center space-x-4 text-sm">
                    <div class="text-right">
                        <div class="font-bold leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-indigo-200">{{ Auth::user()->email }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-indigo-800 hover:bg-rose-600 px-3 py-1.5 rounded-md text-xs font-semibold transition shadow-sm">
                            Logout
                        </button>
                    </form>
                </div>
                @endauth

                @guest
                <div class="space-x-2">
                    <a href="{{ route('login') }}" class="text-sm font-medium hover:text-indigo-200">Login</a>
                    <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-3 py-1.5 rounded-md text-sm font-semibold hover:bg-indigo-50">Register</a>
                </div>
                @endguest
            </div>
        </div>
    </header>

    <!-- Mobile Switcher Bar -->
    @auth
    <div class="md:hidden bg-indigo-900 text-white px-4 py-2 text-xs flex items-center justify-between border-b border-indigo-700">
        <span class="text-indigo-200">Switch Demo:</span>
        <div class="flex space-x-2">
            @foreach(\App\Models\User::all() as $u)
                <a href="{{ route('auth.switch', $u->id) }}" 
                   class="px-2 py-0.5 rounded {{ Auth::id() === $u->id ? 'bg-amber-400 text-indigo-950 font-bold' : 'bg-indigo-800 text-white' }}">
                    {{ explode(' ', $u->name)[0] }}
                </a>
            @endforeach
        </div>
    </div>
    @endauth

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-md shadow-sm flex items-start justify-between">
                <div class="flex items-center text-emerald-800">
                    <span class="text-xl mr-2">✅</span>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-md shadow-sm flex items-start justify-between">
                <div class="flex items-center text-blue-800">
                    <span class="text-xl mr-2">ℹ️</span>
                    <span class="font-medium text-sm">{{ session('info') }}</span>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-md shadow-sm">
                <div class="flex items-center text-rose-800 font-medium text-sm">
                    <span class="text-xl mr-2">⚠️</span>
                    <span>{{ session('error') ?? 'Terjadi kesalahan pada input data:' }}</span>
                </div>
                @if($errors->any())
                    <ul class="mt-2 list-disc list-inside text-xs text-rose-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        Jara Advance To-Do List &copy; {{ date('Y') }} &bull; Praktikum PPK
    </footer>

    @stack('scripts')
</body>
</html>
