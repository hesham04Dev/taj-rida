<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تاج الرضا — بوابة أولياء الأمور</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- @fluxStyles --}}
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen antialiased transition-colors duration-200">
    <nav class="border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg">
                    {{-- <flux:icon.user-group class="size-5 text-white" /> --}}
                    <img class="p-1" src="{{asset('assets/logo.png')}}">
                </div>
                <span class="font-bold text-lg text-zinc-900 dark:text-white">تاج الرضا — بوابة أولياء الأمور</span>
            </div>

            <div class="flex items-center gap-4">
                
                {{-- Premium Theme Switcher Button --}}
                <flux:button
                    x-data="{
                        theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
                        toggle() {
                            this.theme = this.theme === 'dark' ? 'light' : 'dark';
                            localStorage.setItem('theme', this.theme);
                            if (this.theme === 'dark') {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        }
                    }"
                    @click="toggle()"
                    variant="ghost"
                    size="sm"
                    class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white"
                >
                    <flux:icon.moon x-show="theme === 'light'" class="size-5" />
                    <flux:icon.sun x-show="theme === 'dark'" class="size-5" />
                </flux:button>

                <form method="POST" action="{{ route('parent.logout') }}">
                    @csrf
                    <flux:button type="submit" variant="ghost" size="sm" icon="arrow-right-start-on-rectangle" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white">
                        خروج
                    </flux:button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    @fluxScripts
    @livewireScripts
</body>
</html>
