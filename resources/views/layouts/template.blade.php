<!DOCTYPE html>
<html lang="en">

    <link rel="manifest" href="/manifest.json">
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <script defer src="/site.js"></script>

<body>

    <div class="container-fluid p-0 mb-4">
        <nav class="navbar navbar-expand-lg bg-primary fixed-top" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="{{asset('img/logo.png')}}" alt="" width="30" height="24" class="d-inline-block align-text-top"> Perpustakaan
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" aria-current="page" href="/">Pinjam Buku</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('laporan') ? 'active' : '' }}" href="laporan">Laporan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('inventory') ? 'active' : '' }}" href="inventory">Inventory</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('buku') || request()->is('mahasiswa') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Master
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item {{ request()->is('buku') ? 'active' : '' }}" href="buku">Buku</a></li>
                                <li><a class="dropdown-item {{ request()->is('mahasiswa') ? 'active' : '' }}" href="mahasiswa">Mahasiswa</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <div class="container mb-5 mt-5 pt-5">

        @yield('content')

    </div>
    

    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('js/jquery-3.7.1.min.js')}}"></script>

    @yield('script')

</body>

</html>