@php
    $brandUser = auth()->user();
    $primaryColor = $brandUser->primary_color ?? '#4e73df';
    $secondaryColor = $brandUser->secondary_color ?? '#858796';
    $sidebarColor = $brandUser->sidebar_color ?? '#4e73df';
    $companyName = $brandUser->company_name ?? env('APP_NAME');
    $logoPath = $brandUser->logo_path ?? null;
    $faviconPath = $brandUser->favicon_path ?? null;

    // Helper function to adjust color brightness
    $adjustBrightness = function($hex, $steps) {
        $hex = ltrim($hex, '#');
        $r = max(0, min(255, hexdec(substr($hex, 0, 2)) + $steps));
        $g = max(0, min(255, hexdec(substr($hex, 2, 2)) + $steps));
        $b = max(0, min(255, hexdec(substr($hex, 4, 2)) + $steps));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    };

    $sidebarDark = $adjustBrightness($sidebarColor, -30);
    $primaryHover = $adjustBrightness($primaryColor, -15);
    $primaryLinkHover = $adjustBrightness($primaryColor, -20);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ $companyName }} | {{ $pageName ?? '' }}</title>

    @if($faviconPath)
        <link rel="icon" href="{{ Storage::url($faviconPath) }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ Storage::url($faviconPath) }}" type="image/x-icon">
    @endif

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}?v={{ filemtime(public_path('css/sb-admin-2.min.css')) }}" rel="stylesheet">
    
    <!-- UI/UX Refinements -->
    <link href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}" rel="stylesheet">

    {{-- Dynamic Brand Colors --}}
    <style>
        :root {
            --brand-primary: {{ $primaryColor }};
            --brand-secondary: {{ $secondaryColor }};
            --brand-sidebar: {{ $sidebarColor }};
        }

        /* Override primary colors */
        .bg-gradient-primary {
            background-color: var(--brand-sidebar) !important;
            background-image: linear-gradient(180deg, var(--brand-sidebar) 10%, {{ $sidebarDark }} 100%) !important;
        }

        .sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }

        .sidebar .nav-item.active .nav-link,
        .sidebar .nav-item .nav-link:hover {
            color: #fff;
        }

        .btn-primary {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
        }

        .btn-primary:hover {
            background-color: {{ $primaryHover }} !important;
            border-color: {{ $primaryHover }} !important;
        }

        .text-primary {
            color: var(--brand-primary) !important;
        }

        a {
            color: var(--brand-primary);
        }

        a:hover {
            color: {{ $primaryLinkHover }};
        }

        .border-left-primary {
            border-left-color: var(--brand-primary) !important;
        }

        .badge-primary {
            background-color: var(--brand-primary) !important;
        }

        .page-item.active .page-link {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
        }

        .page-link {
            color: var(--brand-primary);
        }

        .card-header {
            border-bottom-color: var(--brand-primary);
        }

        /* Secondary color overrides */
        .text-secondary {
            color: var(--brand-secondary) !important;
        }

        .btn-secondary {
            background-color: var(--brand-secondary) !important;
            border-color: var(--brand-secondary) !important;
        }

        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        /* Brand logo in sidebar */
        .sidebar-brand-icon img {
            max-height: 40px;
            max-width: 40px;
        }
    </style>
    @stack('styles')
    @stack('scripts-top')
</head>

<body id="page-top">
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div id="wrapper">
        <x-partials.navigation-side :logo-path="$logoPath" :company-name="$companyName" />
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <x-partials.navigation-top :logo-path="$logoPath" :company-name="$companyName" />
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h6 class="small mb-4 text-gray-800">HOME
                        @foreach ($links ?? [] as $link)
                            / {{ strtoupper($link) }}
                        @endforeach
                    </h6>
                    {{ $slot }}
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; {{ date('Y') }} {{ $companyName }} | Powered by <a href="//fb.me/mendylivium" target="_blank">MendyFi</a></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}?v={{ filemtime(public_path('js/sb-admin-2.min.js')) }}"></script>
    
    <!-- Mobile Responsiveness Helpers -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var sidebar = document.querySelector('.sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        var sidebarToggle = document.getElementById('sidebarToggle');
        var sidebarToggleTop = document.getElementById('sidebarToggleTop');
        
        function isMobile() {
            return window.innerWidth <= 991;
        }
        
        function closeSidebar() {
            if (sidebar) {
                // On mobile, SB Admin 2 logic is inverted: toggled = visible
                // We need to remove 'toggled' to hide
                sidebar.classList.remove('toggled');
                document.body.classList.remove('sidebar-toggled');
            }
            if (overlay) {
                overlay.classList.remove('show');
            }
        }
        
        function updateOverlay() {
            if (!isMobile() || !overlay) return;
            // On mobile: sidebar.toggled = visible
            if (sidebar && sidebar.classList.contains('toggled')) {
                overlay.classList.add('show');
            } else {
                overlay.classList.remove('show');
            }
        }
        
        // Watch for toggle button clicks
        [sidebarToggle, sidebarToggleTop].forEach(function(btn) {
            if (btn) {
                btn.addEventListener('click', function() {
                    setTimeout(updateOverlay, 50);
                });
            }
        });
        
        // Close sidebar when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', function() {
                closeSidebar();
            });
        }
        
        // Close sidebar when a non-collapsible nav link is clicked on mobile
        document.querySelectorAll('.sidebar .nav-link:not([data-toggle="collapse"])').forEach(function(link) {
            link.addEventListener('click', function() {
                if (isMobile()) {
                    closeSidebar();
                }
            });
        });
        
        // Close sidebar when a collapse sub-item is clicked on mobile
        document.querySelectorAll('.sidebar .collapse-item').forEach(function(item) {
            item.addEventListener('click', function() {
                if (isMobile()) {
                    closeSidebar();
                }
            });
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (!isMobile() && overlay) {
                overlay.classList.remove('show');
            }
        });
    });
    </script>
    @stack('scripts-bottom')
</body>

</html>
