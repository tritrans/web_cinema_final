<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Phim Việt - Vietnamese Movie Streaming')</title>
    <meta name="description" content="@yield('description', 'Xem phim Việt Nam chất lượng cao - Watch high-quality Vietnamese movies')">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS for image handling -->
    <link rel="stylesheet" href="{{ asset('css/image-fixes.css') }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: 'hsl(var(--background))',
                        foreground: 'hsl(var(--foreground))',
                        card: 'hsl(var(--card))',
                        'card-foreground': 'hsl(var(--card-foreground))',
                        popover: 'hsl(var(--popover))',
                        'popover-foreground': 'hsl(var(--popover-foreground))',
                        primary: 'hsl(var(--primary))',
                        'primary-foreground': 'hsl(var(--primary-foreground))',
                        secondary: 'hsl(var(--secondary))',
                        'secondary-foreground': 'hsl(var(--secondary-foreground))',
                        muted: 'hsl(var(--muted))',
                        'muted-foreground': 'hsl(var(--muted-foreground))',
                        accent: 'hsl(var(--accent))',
                        'accent-foreground': 'hsl(var(--accent-foreground))',
                        destructive: 'hsl(var(--destructive))',
                        'destructive-foreground': 'hsl(var(--destructive-foreground))',
                        border: 'hsl(var(--border))',
                        input: 'hsl(var(--input))',
                        ring: 'hsl(var(--ring))',
                    },
                    borderRadius: {
                        lg: 'var(--radius)',
                        md: 'calc(var(--radius) - 2px)',
                        sm: 'calc(var(--radius) - 4px)',
                    },
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js" 
            onerror="this.onerror=null; this.src='https://unpkg.com/lucide@latest/dist/umd/lucide.js';"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-background text-foreground">
    <div class="min-h-screen flex flex-col">
        @include('layouts.header')

        <!-- Page Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Modern Notification System -->
        <x-notification />

        @include('layouts.footer')
    </div>

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
                console.log('Lucide icons initialized successfully');
            } else {
                console.error('Lucide library failed to load');
                // Fallback: show text instead of icons
                document.querySelectorAll('[data-lucide]').forEach(function(element) {
                    const iconName = element.getAttribute('data-lucide');
                    element.innerHTML = iconName.charAt(0).toUpperCase() + iconName.slice(1).replace(/-/g, ' ');
                    element.style.fontSize = '12px';
                    element.style.fontWeight = 'bold';
                });
            }
        });
    </script>
    
    <!-- Notification System -->
    <script src="{{ asset('js/notifications.js') }}"></script>
    
    <!-- API Client -->
    <script src="{{ asset('js/api.js') }}"></script>
    
    @stack('scripts')
</body>
</html>