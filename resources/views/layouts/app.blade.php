<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JARA - To-Do List')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <nav class="bg-indigo-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="font-bold text-xl tracking-wider">JARA</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.users.index') }}" class="bg-indigo-800 hover:bg-indigo-900 px-3 py-2 rounded-md text-sm font-medium border border-indigo-400">Panel Admin</a>
                        @endif
                    @endauth
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm font-medium bg-indigo-700 px-3 py-1 rounded-full">
                            {{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-md font-medium transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm hover:underline">Login</a>
                        <a href="{{ route('register') }}" class="text-sm bg-white text-indigo-600 font-semibold px-3 py-1.5 rounded-md shadow hover:bg-gray-100">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
<<<<<<< HEAD
    <footer class="bg-white border-t py-4 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} JARA - Advance To-Do List Application.
    </footer>

</body>
</html>
=======
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        Jara Advance To-Do List &copy; {{ date('Y') }} &bull; Praktikum PPK
    </footer>

    @stack('scripts')
</body>
</html>
>>>>>>> origin/feature/invitation-system
