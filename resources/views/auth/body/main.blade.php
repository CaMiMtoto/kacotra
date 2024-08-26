<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />

        <!-- Favicons -->
        <link href="{{ asset('assets/img/logos/cement-bag-01.png') }}" rel="icon">
        <link href="{{ asset('assets/img/logos/cement-bag-01.png') }}" rel="apple-touch-icon">

        <title>KACOTRA Inventory Management System</title>

        <!-- Style CSS -->
        <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />

        <!-- Icons -->
        <script data-search-pseudo-elements="" defer="" src="{{ asset('assets/js/font-awesome.all.min.js') }}"></script>
        <script src="{{ asset('assets/js/feather-icons.min.js') }}"></script>
    </head>

    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                <!-- BEGIN: Content -->
                @yield('content')
                <!-- END: Content -->
                </main>
            </div>

            <!-- BEGIN: Footer -->
            <div id="layoutAuthentication_footer">
                <footer class="footer-admin mt-auto footer-dark">
                    <div class="container-xl px-4">
                        <div class="row">
                            <div class="col-md-6 small">Copyright © KACOTRA Ltd <script>document.write(new Date().getFullYear())</script></div>
                            <div class="col-md-6 text-md-end small">
                                <a href="#">Privacy Policy</a>
                                ·
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
            <!-- END: Footer -->
        </div>

        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
