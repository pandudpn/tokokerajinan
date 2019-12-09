<!DOCTYPE html>
<html lang="en">
<head id="headerTop">
    <meta charset="UTF-8">
    <meta name="title" content="Toko Kerajinan Indonesia">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href={{ asset('/assets/images/icon.png') }} />
    <link rel="stylesheet" href={{ asset('/assets/bootstrap/css/bootstrap.min.css') }}>
    <link rel="stylesheet" href={{ asset('/assets/css/style.css') }}>
    <link rel="stylesheet" href={{ asset('/assets/css/bs-stepper.min.css') }}>
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/3.6.95/css/materialdesignicons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/css/star-rating.min.css" media="all" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/themes/krajee-fa/theme.css" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('/assets/css/chartjs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/jquery_datetimepicker/jquery.datetimepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="{{ asset('/assets/slick/slick/slick-theme.css') }}">
<!--    <script type="text/javascript" 
src="https://platform-api.sharethis.com/js/sharethis.js#property=5d22136ae6c4ff00125088f6&product=inline-share-buttons"></script> -->
    <title id="titleHead">Toko Kerajinan</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <a class="navbar-brand mr-5" href="{{ url('/') }}">
            <img src="{{ asset('/assets/images/logo2.png') }}" width="200" height="20">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-5">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Kategori
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown" id="dropdown-kategori">
                    </div>
                </li>
            </ul>
            <form action="{{ url('/p') }}" class="w-50" method="GET" id="formSearch">
                <div class="input-group">
                    <input type="text" class="form-control" name="s" placeholder="Cari produk...." aria-label="Cari Produk" aria-describedby="cari-produk">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit"><i class="mdi mdi-magnify"></i></button>
                    </div>
                </div>
            </form>
            <ul class="navbar-nav ml-auto">
                @if (session('login'))
                    <li class="nav-item _cart-count pr-5">
                        <a class="nav-link" href="{{ url('/cart') }}"><i class="mdi mdi-cart-outline"></i> <span class="d-none d-sm-inline-block">{{ session('cart') != null ? count(session('cart')) : '0' }}</span><span class="d-inline-block d-sm-none">5</span></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="tokoDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Toko
                        </a>
                        <div class="dropdown-menu" aria-labelledby="tokoDropdown" id="dropdown-toko">
                            @if (session('toko') == null)
                                <a href="{{ url('/toko/buat') }}" class="dropdown-item">Buat Toko</a>
                            @else
                                <a href="{{ url('/toko/'.session('slug')) }}" class="dropdown-item">Toko Saya</a>
                                <a href="{{ url('/toko/produk') }}" class="dropdown-item">Produk Saya</a>
                                <a href="{{ url('/toko/penjualan') }}" class="dropdown-item">Penjualan</a>
                            @endif
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ session('username') }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="userDropdown" id="dropdown-user">
                            <p class="dropdown-item">Rp {{ number_format(session('uang'), 0, '.', '.') }}</p>
                            <div class="dropdown-divider"></div>
                            <a href="{{ url('/pembelian') }}" class="dropdown-item">Pembelian</a>
                            <a href="{{ url('/logout') }}" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/register') }}">Daftar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/login') }}">Masuk</a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>

    {{-- content --}}
    @yield('content')
    {{-- content --}}
    <div class="modal fade" id="ModalGue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="ModalHeader"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class='fa fa-times-circle'></i></button>
                </div>
                <div class="modal-body" id="ModalContent"></div>
                <div class="modal-footer" id="ModalFooter"></div>
            </div>
        </div>
    </div>

    <script src={{ asset('/assets/jquery/jquery.min.js') }}></script>
    <script src={{ asset('/assets/jquery_datetimepicker/jquery.datetimepicker.full.js') }}></script>
    <script src={{ asset('/assets/bootstrap/js/bootstrap.min.js') }}></script>
    <script src={{ asset('/assets/bootstrap/js/popper.min.js') }}></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="{{ asset('/assets/js/star-rating.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/assets/js/jquery.steps.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/assets/js/bs-stepper.min.js') }}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/themes/krajee-svg/theme.js"></script>
    <script src="{{ asset('/assets/divider/dist/number-divider.min.js') }}"></script>
    <script src="{{ asset('/assets/js/chartjs.min.js') }}"></script>
    <script src="{{ asset('/assets/slick/slick/slick.min.js') }}"></script>
<script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=5d22136ae6c4ff00125088f6&product=inline-share-buttons"></script>
    <script>
        $(document).ready(function(){
            ListKategori();
        })

        function ListKategori(){
	    var URL = '{!! url("/api/p/kategori") !!}';
            $.ajax({
                url: URL,
                type: 'GET',
                dataType: 'json',
                success: function(data){
                    var html = '';
                    if(data.status === 200){
                        if(data.data.length > 0){
                            $.map(data.data, (result, index) => {
                                var slug = result.slug_kategori
                                var base = '{!! url("/p/") !!}';
                                var url  = base+'?k='+slug
                                html += "<a class='dropdown-item' href='" + url + "'>" + result.nama_kategori + "</a>"
                            })
                        }else{
                            html += 'Tidak ada Kategori';
                        }
                    }else{
                        html += 'Tidak ada kategori'
                    }

                    $('#dropdown-kategori').html(html)
                }
            })
        }
    </script>
    @yield('script')
</body>
</html>
