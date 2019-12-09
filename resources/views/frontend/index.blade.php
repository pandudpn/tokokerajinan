@extends('layouts.frontend')
@section('content')
  {{-- content-wrapper --}}
  <div class="content-wrapper">
    <div class="_slider-bungkus mx-auto">
      <div class="slider">
        @foreach ($banner as $data)
          <div class="_cover-slider">
            <a href="{{ url('/event/'.$data->slug) }}">
              <img src="{{ asset('/assets/images/banner/'.$data->foto) }}">
            </a>
          </div>
        @endforeach
      </div>
    </div>
    <div class="mt-5"></div>
    <div id="list_kategori"></div> <!-- result list-kategori ajax -->
    <div id="popular" class="d-none d-sm-block"></div>
    <div id="popular2" class="d-block d-sm-none"></div>
  </div> <!-- .// content-wrapper -->
@endsection

@section('script')
  <script>
    $(document).ready(function(){
      $('.slider').slick({
        autoplay: true,
        centerMode: true,
        centerPadding: '100px',
        slideToShow: 1,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 1
            }
          },
          {
            breakpoint: 480,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: '40px',
              slidesToShow: 1
            }
          }
        ]
      });

      listKategori();
      popular();
    });

    function spinner(){
      var load  = '<div class="spinner">'; /* spinner */
          load += '<div class="double-bounce1"></div>';
          load += '<div class="double-bounce2"></div>';
          load += '</div>'; /* end spinner */

      return load;
    }

    function listKategori(){
      $.ajax({
        url: '{!! url("/api/p/kategori") !!}',
        type: 'get',
        dataType: 'json',
        beforeSend: function(){
          $('#list_kategori').html(spinner());
        },
        success: function(data){
          console.log(data);
          var html = '<div class="row">';
          $.map(data.data, (kategori, index) => {
            var base  = '{!! url("/p") !!}';
            var asset = '{!! asset("/assets/images/kategori") !!}';

            var url   = base + '?k=' + kategori.slug_kategori;
            var image = asset + '/' + kategori.foto_kategori;

            html += '<div class="col-md-2 col-6 mb-3">'; /* columns 1 */
            html += '<div class="box-kategori">'; /* box-kategori */
            html += '<a href="' + url + '">'; /* link to search kategori */
            html += '<div class="box-kategori-header">'; /* box-kategori header */
            html += '<div class="image-kategori">'; /* image kategori */
            html += '<img src="' + image + '">'; /* image */
            html += '</div>'; /* end image kategori */
            html += '</div>'; /* end box kategori header */
            html += '<div class="box-kategori-text text-center">'; /* box kategori text */
            html += '<p>' + kategori.nama_kategori + '</p>';
            html += '</div>'; /* end box kategori text */
            html += '</a>'; /* end link */
            html += '</div>'; /* end box-kategori */
            html += '</div>'; /* end columns 1 */
          });
          html += '</div>';

          $('#list_kategori').html(html);
        }
      });
    }

    function popular(){
      $.ajax({
        url: '{!! url("/api/p/popular") !!}',
        type: 'get',
        dataType: 'json',
        beforeSend: function(){
          $('#popular').html(spinner());
        },
        success: function(data){
          setTimeout(() => {
            $('#popular').html(html(data.data, 1));
	    $('#popular2').html(html(data.data, 2));
            $('.rate').rating({displayOnly: true, showCaption: false});
          }, 700);
        },
        complete: function(){
          setTimeout(() => {
            $('.slider-popular').slick({
              infinite: false
            });
          }, 700);
        }
      });
    }

    function html(data, type){
      var html = '<div class="best-product">'; /* best product */
      html += '<div class="product-header">'; /* product header */
      html += '<h5>Produk Terpopuler</h5>';
      html += '</div>';
      html += '<div class="product-body">'; /* product body */
      if(type === 1){
         html += `<div class="slider-popular" data-slick='{"slidesToShow": 6, "slidesToScroll": 2}'">`; /* slider popular d-none */
      }else{
         html += `<div class="slider-popular" data-slick='{"slidesToShow": 1, "slidesToScroll": 1}'">`; /* slider popular d-block */
      }

      $.map(data, (result, index) => {
        var key     = 'pandudpn09';
        // encryption
        var ec      = result.id_produk.toString();
        var encrypt = CryptoJS.AES.encrypt(ec, key) // end encryption

        var base  = '{!! url("/p") !!}';
        var url   = base + '/' + result.slug_produk + '/' + btoa(encrypt); /* end url encryption */

        var asset = '{!! asset("/assets/images/product") !!}';
        var image = asset + '/' + result.nama_foto;

        var rate = 0;

        if(result.totalRating === null){
          rate = 0;
        }else{
          rate = result.totalRating;
        }

        html += '<div class="product-body-box">'; /* product body box */
        html += '<a href="' + url + '">'; /* link to details */
        html += '<div class="product-box-header">'; /* product header */
        html += '<img src="' + image + '">'; /* image */
        html += '</div>'; /* end product header */
        html += '<div class="product-box-body">'; /* product body */
        html += '<div class="title-product-body">'; /* title product */
        html += '<h6>' + result.nama_produk + '</h6>';
        html += '</div>'; /* end title product */
        html += '<div class="price-product-body">'; /* price product */
        html += '<h6>Rp ' + new Intl.NumberFormat('id').format(result.harga) + '</h6>';
        html += '</div>'; /* end price product */
        html += '<div class="rating-product-body pt-3">'; /* rating product */
        html += '<div class="row">'; /* row */
        html += '<div class="col-md-6">'; /* columns 6 */
        html += '<input id="input-3" name="input-3" value="' + rate + '" class="rate rating-loading" data-size="xs">'; /* rating */
        html += '</div>'; /* end columns 6 */
        html += '<div class="col-md-6 text-right">'; /* columns 6 right */
        html += '<small><i>' + new Intl.NumberFormat('id').format(result.totalPesanan) + ' Terjual</i></small>'
        html += '</div>'; /* end columns */
        html += '</div>'; /* end row */
        html += '</div>'; /* end rating product */
        html += '</div>'; /* end product body */
        html += '</a>'; /* end link to details */
        html += '</div>'; /* end product body box */
      });
      
      html += '</div>'; /* end slider popular */
      html += '</div>'; /* end product body */
      html += '</div>'; /* end best product */

      return html;
    }
  </script>
@endsection
