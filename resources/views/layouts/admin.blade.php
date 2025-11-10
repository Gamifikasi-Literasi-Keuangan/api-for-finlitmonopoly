<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - AdminLTE v4</title>
    
    <!-- AdminLTE 4 CSS -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('src/plugins/fontawesome-free/css/all.min.css') }}">
    
    <!-- Custom styles for images -->
    <style>
        .brand-image {
            width: 33px !important;
            height: 33px !important;
            opacity: 0.8;
            margin-right: 0.5rem;
        }
        .user-image {
            width: 25px !important;
            height: 25px !important;
        }
        .img-circle {
            border-radius: 50% !important;
        }
        .user-panel .image {
            margin-right: 10px;
        }
        .user-panel .image img,
        .user-panel .image i {
            width: 34px;
            height: 34px;
        }
        .dropdown-menu .img-size-50 {
            width: 50px !important;
            height: 50px !important;
        }
    </style>
    
    <!-- Custom styles -->
    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        
        <!-- Navbar -->
        @include('partials.navbar')
        
        <!-- Sidebar -->
        @include('partials.sidebar')
        
        <!-- Main Content -->
        <main class="app-main">
            <!-- Content Header -->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">@yield('page-title', 'Dashboard')</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="app-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        @include('partials.footer')
        
    </div>
    
    <!-- AdminLTE 4 Scripts -->
    <script src="{{ asset('src/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('src/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    
    <!-- Custom scripts -->
    @stack('scripts')
</body>
</html>