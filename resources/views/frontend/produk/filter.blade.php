@extends('layouts.frontend')

@section('content')
    <div id="result"></div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            var urlParams   = new URLSearchParams(window.location.search);
            var search      = urlParams.get('s');
            var kategori    = urlParams.get('k');
            if(kategori !== null && search === null){
                getProduk(null, kategori)
                $('#titleHead').html(kategori + ' - Toko Kerajinan');
                $('#headerTop').prepend('<meta name="description" content="' + kategori + ' - Toko Kerajinan">');
            }else if(kategori === null && search !== null){
                getProduk(search, null)
                $('#titleHead').html(search + ' - Toko Kerajinan');
                $('meta[name="title"]').attr('content', '' + search + ' - Toko Kerajinan')
                $('#headerTop').prepend('<meta name="description" content="' + search + ' - Toko Kerajinan">');
            }else{
                getProduk();
            }
            
        })

        function getProduk(search, kategori){
            var base= '{!! url('/api/p') !!}'
            var URL = '';
            if(search === undefined && kategori === undefined){
                URL = base
            }else if(search === null && kategori !== undefined){
                URL = base + '/?k='+kategori
            }else if(search !== undefined && kategori == null){
                URL = base + '/?s='+search
            }else if(search !== null && kategori !== null){
                URL = base + '/?s=' + search + '&k=' + kategori
            }

            var key     = 'pandudpn09';

            $.ajax({
                url: URL,
                type: 'GET',
                cache: false,
                dataType: 'json',
                beforeSend: function(){
                    $('#result').html('<div style="height: 300px; line-height: 300px; text-align: center;"><i class="mdi mdi-loading mdi-spin mdi-48px" style="display: inline-block; vertical-align: middle; line-height: normal; color: #b0b0b0"></i></div>')
                },
                success: function(data){
                    var html = '<div class="content-wrapper">'

                    if(data.status === 200){ // jika api/produk sukses
                        var base    = '{!! asset('/assets/images/product/') !!}'
                        // header
                        html += '<div class="col-md-12 mt-3 text-center">'


                        if(data.data.length > 0){ // jika data lebih dari 0
                            // row
                            html += '<h4>Total pencarian ada <b><i>' + data.data.length + '</i></b> produk</h4>'
                            html += '<div class="row">'

                            
                            $.map(data.data, (result, index) => {
                                // url image
                                var rate    = '';
                                if(result.totalRating === null){
                                    rate    = 0;
                                }else{
                                    rate    = result.totalRating;
                                }
                                var image   = result.nama_foto
                                var imageUrl= base+ '/' +image // end url image
                                // membuat mata uang
                                var harga   = new Intl.NumberFormat(['bal', 'id']).format(result.harga) // end mata uang

                                // encryption
                                var ec      = result.id_produk.toString();
                                var encrypt = CryptoJS.AES.encrypt(ec, key) // end encryption

                                // url details
                                var baseUrl = '{!! url('/p') !!}'
                                var url     = baseUrl + '/' + result.slug_produk + '/' + btoa(encrypt) // end url details

                                // membuat card
                                html += '<div class="column">'
                                html += '<a href="' + url + '">'
                                html += '<div class="card">'
                                html += '<div class="cover-image">'
                                html += '<img class="card-img-top" src="' + imageUrl + '" />'
                                html += '</div>'
                                html += '<div class="card-body">' // card-body
                                html += '<h6 class="card-title">' + result.nama_produk + '</h6>'
                                html += '<div class="row">' // row
                                html += '<div class="col-md-6 col-5">'
                                html += '<p class="card-price">Rp ' + harga + '</p>'
                                html += '</div>'
                                html += '<div class="col-md-6 col-7">'
                                html += '<input id="input-'+ index + '" name="input-'+index+'" class="rating rating-loading" value="' + rate + '" data-size="xs" />'
                                html += '</div>'
                                html += '</div>' // end row
                                html += '</div>' // card-body
                                html += '</div></a></div>' // column, a, card
                            })
                            html += '</div>'
                        }else{ // jika jumlah data kosong
                            if(search){
                                html += '<h4 class="text-danger">Produk <b><i>' + search + '</i></b> tidak ada</h4>'
                            }else if(kategori){
                                html += '<h4 class="text-danger">Produk <b><i>' + kategori + '</i></b> tidak ada</h4>'
                            }
                        }
                    }else if(data.status === 404){ // jika api/produk gagal
                        html += '<h4 class="text-danger">Terjadi kesalahan pada server. Silahkan coba beberapa saat lagi.</h4>'
                    } // end checking

                    html += '</div></div>'

                    setTimeout(() => {
                        $('#result').html(html)
                        $('.rating').rating({
                            showCaption: false,
                            displayOnly: true
                        })
                    }, 1000)
                }
            })
        }
    </script>
@endsection