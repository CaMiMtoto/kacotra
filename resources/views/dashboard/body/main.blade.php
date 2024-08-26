<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>KACOTRA Dashboard Inventory</title>

        <!-- Favicons -->
        <link href="{{ asset('assets/img/logos/cement-bag-01.png') }}" rel="icon">
        <link href="{{ asset('assets/img/logos/cement-bag-01.png') }}" rel="apple-touch-icon">

        <!-- Styles CSS -->
        <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />

        <!-- Icons -->
        <script data-search-pseudo-elements="" defer="" src="{{ asset('assets/js/font-awesome.all.min.js') }}"></script>
        <script src="{{ asset('assets/js/feather-icons.min.js') }}"></script>

        <!-- Custom CSS for specific page.  -->
        @yield('specificpagestyles')
    </head>

    <body class="nav-fixed">
        <!-- BEGIN: Navbar Brand -->
        @include('dashboard.body.header')
        <!-- END: Navbar Brand -->

        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <!-- BEGIN: Sidenav -->
                @include('dashboard.body.sidebar')
                <!-- END: Sidenav -->
            </div>


            <div id="layoutSidenav_content">
                <main>
                <!-- BEGIN: Content -->
                    @yield('content')
                <!-- END: Content -->
                </main>

                <!-- BEGIN: Footer  -->
                @include('dashboard.body.footer')
                <!-- END: Footer  -->
            </div>
        </div>

        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.js') }}"></script>

        <!-- Custom JS for specific page.  -->
        @yield('specificpagescripts')
    </body>
</html>
