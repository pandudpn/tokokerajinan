@extends('layouts.frontend')

@section('content')
  <div id="result"></div>
@endsection

@section('script')
  <script>
    $(document).ready(function(){
      var slug    = '{{ Request::segment(2) }}'
      var id      = '{{ Request::segment(3) }}'
      getData(slug, atob(id));
    });

    $(document).on('click', '._change-photo', function(e){
      e.preventDefault();

      var url = $(this).data('url');
      $('._details-foto-view').attr('src', url);
    });

    $(document).on('click', '#minus', function(e){
      e.preventDefault();

      var total = $('#total').val();

      Jumlah(total, 'kurang');
    });

    $(document).on('click', '#plus', function(e){
      e.preventDefault();

      var total = $('#total').val();

      Jumlah(total, 'tambah');
    });

    $(document).on('submit', '#addingCart', function(e){
      e.preventDefault();

      var id  = $('#produk_id').val();
      var qty = $('#total').val();

      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: {
          '_token': '{{ csrf_token() }}',
          'id': id,
          'qty': qty
        },
        dataType: 'json',
        cache: false,
        success: function(data){
          if(data.status === 406){
            window.location.replace('/login');
          }else if(data.status === 201){
            var produk  = data.data;
            var base    = '{!! url("/assets/images/product") !!}';
            var image   = base + '/' + produk.foto;
            var a = '<div class="col-md-12">' + data.pesan + '</div>';
            a += '<div class="_cover-foto-modal d-block mx-auto">';
            a += '<img src="' + image + '" />';
            a += '</div>';
            a += '<div class="col-md-12 mt-2 text-center">';
            a += '<p>Jumlah Barang : ' + qty + '</p>';
            a += '</div>';

            $('#ModalHeader').html('Sukses');
            $('#ModalContent').html(a);
            $('#ModalFooter').html('<button type="button" data-dismiss="modal" class="btn btn-warning">Lanjut, Belanja</button><a href={{ url("/cart") }} class="btn btn-success">Menuju Keranjang Belanja</a>')
            $('#ModalGue').modal('show');
          }
        }
      });
    });

    function Jumlah(total, tipe){
      var t = 0;
      if(tipe === 'tambah'){
        t = parseInt(total) + 1;
      }else if(tipe === 'kurang'){
        t = parseInt(total) - 1;
      }

      $('#total').val(t);
      cekTotal(t);
    }

    function cekTotal(total){
      var p = $('#stok').val();
      if(total > 1){
        $('#minus').removeAttr('disabled');
      }else if(total === 1){
        $('#minus').attr('disabled', 'disabled');
      }
      
      if(total < p){
        $('#plus').removeAttr('disabled');
        console.log(total);
      }else if(total == p){
        $('#plus').attr('disabled', 'disabled');
      }
    }

    function getData(slug, id){
      // decryption
      var key     = 'pandudpn09';
      var d       = CryptoJS.AES.decrypt(id, key);
      var decrypt = d.toString(CryptoJS.enc.Utf8)

      var baseURL = '{!! url('/api/p') !!}'
      var URL     = baseURL + '/' + slug + '/' + decrypt

      $.ajax({
        url: URL,
        type: 'get',
        dataType: 'json',
        beforeSend: function(){
          $('#result').html('<div style="height: 300px; line-height: 300px; text-align: center;"><i class="mdi mdi-loading mdi-spin mdi-48px" style="display: inline-block; vertical-align: middle; line-height: normal; color: #b0b0b0"></i></div>')
        },
        success: function(data){
          var html = '<div class="content-wrapper">'; // content-wrapper
          if(data.status === 200){
            if(data.data.produk !== null){

              var produk  = data.data.produk
              var ratingProduk  = '';
              if(produk.totalRating === null){
                ratingProduk = 0;
              }else{
                ratingProduk = produk.totalRating;
              }

              $('#titleHead').html(produk.nama_produk + ' - Toko Kerajinan');
              $('#headerTop').prepend('<meta name="description" content="' + produk.desc_produk + '">');

              var uToko   = '{!! url('/') !!}'; 
              var slugToko= uToko + '/toko/' + produk.slug_toko; // slug toko

              var u       = '{!! asset('/assets/images/') !!}'; // url for getting photo
              var imageC  = u + '/product/' + data.data.foto[0].nama_foto; // url for getting product photo from public
              var imageTo = '';
              
              if(produk.foto_toko === ''){ // checking if there's has photo or not
                imageTo += u + '/toko/toko.png'; // no photo
              }else{
                imageTo += u + '/toko/' + produk.foto_toko; // has photo
              }

              html += '<div class="row">'; // row
              // box left
              html += '<div class="col-md-12 col-lg-9 _box-left">'; 
              html += '<div class="row">'; // row nested on box-left
              
              // sebelah kiri
              html += '<div class="col-md-12 col-lg-5 _details-foto">'; // details foto left side
              html += '<div class="row">';
              html += '<div class="col-md-12">';
              html += '<div class="_cover-details-foto">'; // details foto big
              html += '<img src="' + imageC + '" class="_details-foto-view">';
              html += '</div></div>'; // ends col-12 and _cover-details-foto
              html += '<div class="col-md-12">';
              html += '<div class="slick-slider" id="_details-slick">'; //slider foto
              for(var i = 0; i < data.data.foto.length; i++){
                var imageSlider = u + '/product/' + data.data.foto[i].nama_foto
                html += '<figure>';
                html += '<a href="#" class="_change-photo" data-url="' + imageSlider +'">';
                html += '<img src="' + imageSlider +'">';
                html += '</a></figure>'; // end figure and a
              }
              html += '</div></div>'; // end slick-slider and col-12
              html += '</div></div>'; // end row and _details-foto

              // sebelah kanan
              html += '<div class="col-md-12 col-lg-7 _details-produk">'; // details-produk right side
              html += '<h4 class="_details-produk-judul">' + produk.nama_produk + '</h4><hr/>'; // title product
              html += '<div class="row">'; // row
              html += '<div class="col-md-5 col-12">'; // price
              html += '<p class="_details-produk-harga">Rp ' + new Intl.NumberFormat(['bal', 'id']).format(produk.harga) + '</p>';
              html += '</div>'; // end price
              html += '<div class="col-md-7 col-12 _details-produk-rating">' // rating
              html += '<input class="rating rating-loading" value="' + ratingProduk + '" data-size="xs" />' // rating-star from bootstrap-star-rating
              html += '</div>'; // end rating 
              html += '<div class="col-md-12 col-12 text-right"><div class="sharethis-inline-share-buttons"></div></div>'; // share social media
              html += '</div>'; // end row

              // informasi produk
              html += '<div class="_details-informasi-produk">'; // informasi produk
              html += '<p class="_details-produk-informasi-judul">Informasi Produk</p>';
              html += '<ul class="list-group">'; // list-group

              // produk terjual
              html += '<li class="list-group-item">'; // list-group item
              html += '<div class="row">'; // row
              html += '<div class="col-6">'; // col-6
              html += '<i class="mdi mdi-truck-delivery mr-2"></i> Terjual';
              html += '</div>'; // end col-6 left side
              html += '<div class="col-6 text-right">' + produk.totalPesanan + '</div>'; // right side amount of products sell's
              html += '</div></li>'; // end of row and end list-group item

              // stok
              html += '<li class="list-group-item">'; // list-group item
              html += '<div class="row">'; // row
              html += '<div class="col-6">'; // col-6
              html += '<i class="mdi mdi-package-variant-closed mr-2"></i> Stok';
              html += '</div>'; // end col-6 left side
              html += '<div class="col-6 text-right">' + produk.stok + '</div>'; // right side stock
              html += '<input type="hidden" value="' + produk.stok + '" id="stok" />'; // right side stock
              html += '</div></li>'; // end of row and end list-group item

              // berat
              html += '<li class="list-group-item">'; // list-group item
              html += '<div class="row">'; // row
              html += '<div class="col-6">'; // col-6
              html += '<i class="mdi mdi-weight-gram mr-2"></i> Berat';
              html += '</div>'; // end col-6 left side
              html += '<div class="col-6 text-right">' + produk.berat + '</div>'; // right side weight's of product
              html += '</div></li>'; // end of row and end list-group item

              html += '</ul></div>'; // end of list-group and _details-informasi-produk

              // cart
              html += '{!! Form::open(["class" => "form-horizontal", "id" => "addingCart", "url" => url("/p/addingToCart")]) !!}'; // form adding to cart
              html += '<input type="hidden" value="' + decrypt + '" name="produk" id="produk_id" />';
              html += '<div class="row">'; // row cart
              html += '<div class="col-md-12 _details-tambah-cart">'; // col-10 for add to cart
              html += '<div class="row">'; // row
              html += '<div class="col-md-2 col-12 text-center">'; // button minus column
              html += '<button type="button" class="btn btn-outline-warning" id="minus" disabled><i class="mdi mdi-minus"></i></button>'; // button minus
              html += '</div>'; // end of button minus column
              html += '<div class="col-md-2 col-12">'; // col-4 for input
              html += '<input type="text" class="form-control" id="total" readonly="readonly">'; // input
              html += '</div>'; // end of input column
              html += '<div class="col-md-2 col-12 text-center">'; // button plus columns
              html += '<button type="button" class="btn btn-outline-warning" id="plus"><i class="mdi mdi-plus"></i></button>'; // button plus
              html += '</div>';
              html += '<div class="col-md-4 col-12">';
              html += '<button type="submit" class="btn btn-outline-warning" style="border-radius: 5px !important;" id="addCart"><i class="mdi mdi-cart-plus"></i> Tambahkan Keranjang</button>'
              html += '</div>';
              html += '</div></div>'; // end row and end _details-tambah-cart and end of row before _details-tambah-cart
              html += '{!! Form::close() !!}'; // end form adding to cart
              html += '</div>'; // end row
              html += '</div></div></div>';

              // box kanan atau columns sebelah kanan (detail toko)
              html += '<div class="col-md-12 col-lg-3 _box-right">'; // box right side
              // foto toko
              html += '<img src="' + imageTo + '" class="d-block mx-auto" />'; // photo store
              // nama toko
              html += '<a href="' + slugToko + '" class="_details-toko-name d-block text-center">' + produk.nama_toko + '</a>'; // name of store
              // informasi toko
              html += '<div class="row _details-information-toko">'; // information store
              html += '<div class="col-md-4">';
              html += '<p><i class="mdi mdi-map-marker"></i> Lokasi</p>'; // location store left side
              html += '</div>'; // end col-md-4
              html += '<div class="col-md-8 text-right"><p>' + GetKota(produk.kota) + '</p></div>'; // location store right side
              html += '</div></div>'; // end of _details-information-toko and _box-right
              html += '</div>'; // end of row

              // description, review and question
              html += '<section id="_details-desc">'; // section _details-desc
              html += '<div class="col-md-12">'; // col-md-12
              html += '<nav>'; // nav
              html += '<ul class="nav nav-tabs" id="myTab" role="tablist">'; // nav-tabs
              html += '<li class="nav-item">'; // nav-item
              html += '<a class="nav-link active" id="desc-tab" data-toggle="tab" href="#desc" role="tab" aria-controls="desc" aria-selected="true">'; // nav-link
              html += '<i class="mdi mdi-file-document-box-outline"></i> Deskripsi'; // icon + deskripsi
              html += '</a></li>'; // end nav-item
              html += '<li class="nav-item">'; // nav-item
              html += '<a class="nav-link" id="ulasan-tab" data-toggle="tab" href="#ulasan" role="tab" aria-controls="ulasan" aria-selected="true">'; // nav-link
              html += '<i class="mdi mdi-comment-check-outline"></i> Ulasan'; // icon + ulasan
              html += '</a></li>'; // end nav-item
              html += '<li class="nav-item">'; // nav-item
              html += '<a class="nav-link" id="qna-tab" data-toggle="tab" href="#qna" role="tab" aria-controls="qna" aria-selected="true">'; // nav-link
              html += '<i class="mdi mdi-comment-question"></i> Pertanyaan'; // icon + qna
              html += '</a></li>'; // end nav-item
              html += '</ul>'; // end nav-tabs
              html += '<div class="tab-content" id="myTabContent">'; // tab-content
              html += '<div class="tab-pane fade show active" id="desc" role="tabpanel" aria-labelledby="desc-tab">'; // tab-pane description
              html += produk.desc_produk; // data description product
              html += '</div>' // end tab-pane description
              html += '<div class="tab-pane fade" id="ulasan" role="tabpanel" aria-labelledby="ulasan-tab">'; // tab-pane ulasan
              html += '<div class="row">';
              if(data.data.ulasan.length > 0){
                for(var c = 0; c < data.data.ulasan.length; c++){
                  html += '<div class="col-md-6">'; // col-md-6
                  html += '<div class="_box-comments">'; // box-comments
                  html += '<div class="_box-comments-name">'; // box-comments-name
                  html += '<h6>' + data.data.ulasan[c].nama + '</h6>'; // data for name review
                  html += '<input class="rating rating-loading" value="' + data.data.ulasan[c].rating + '" data-size="xs" />'
                  html += '<hr>';
                  html += '</div>'; // end _box-comments-name
                  html += '<div class="_box-comments-isi">'; // _box-comments-isi
                  html += data.data.ulasan[c].isi_komentar; // data for comments
                  html += '</div>'; // end _box-comments-isi
                  html += '</div>'; // end _box-comments
                  html += '</div>'; // end col-md-6
                }
              }else{
                html += '<div class="col-md-12 text-center"><h3 class="text-danger mt-3">Belum ada Ulasan.</h3></div>'; // jika tidak ada ulasan
              }
              html += '</div></div>'; // end row and end of tab-fane ulasan
              html += '<div class="tab-pane fade" id="qna" role="tabpanel" aria-labelledby="qna-tab">'; // tab-fane pertanyaan
              html += '<div class="row">';
              if(data.data.tanya.length > 0){
                for(var d = 0; d < data.data.tanya.length; d++){
                  html += '<div class="col-md-6">'; // col-md-6
                  html += '<div class="_box-comments">'; // box-comments
                  html += '<div class="_box-comments-name">'; // box-comments-name
                  html += '<h6>' + data.data.tanya[d].nama + '</h6>'; // data for name review
                  html += '<hr>';
                  html += '</div>'; // end _box-comments-name
                  html += '<div class="_box-comments-isi">'; // _box-comments-isi
                  html += data.data.tanya[d].isi_pertanyaan; // data for comments
                  html += '</div>'; // end _box-comments-isi
                  html += '</div>'; // end _box-comments
                  html += '</div>'; // end col-md-6
                }
              }else{
                html += '<div class="col-md-12 text-center"><h3 class="text-danger mt-3">Belum ada Pertanyaan.</h3></div>'; // jika tidak ada pertanyaan
              }
              html += '</div></div>'; // end row and end of tab-fane pertanyaan
              html += '</div>'; // end tab-content
              html += '</nav>'; // end tabs
              html += '</div>'; // end col-md-12
            }
          }
          html += '</div>'

          $('#result').html(html);
          $('.rating').rating({
            displayOnly: true
          });

          $('#total').val('1');

          $('.slick-slider').slick({
            autoplay: true,
            dots: true,
            infinite: true,
            slidesToShow: 2,
            slidesToScroll: 1
          });
          
        }
      });
    }

    function GetKota(id){
      var URL = '{!! url("/api/rajaongkir/city") !!}';
      var kota;
      $.ajax({
        url: URL,
        type: 'get',
        async: false,
        data: {'id':id},
        dataType: 'json',
        success: function(result){
          var ro  = result.rajaongkir.results;
          kota    = ro.city_name + ', ' + ro.province;
        }
      });
      return kota;
    }
  </script>    
@endsection
