<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>IMSDashboard Inventory</title>

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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <style>
            *{
                font-family: "Figtree", sans-serif !important;
            }
            .logo{
                height: 72px;
            }
        </style>
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
{{--                    alerts--}}
                    <x-alerts/>

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
