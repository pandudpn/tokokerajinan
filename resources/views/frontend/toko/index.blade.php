@extends('layouts.frontend')
@section('content')
  <div class="content-wrapper">
      {{-- kiri --}}
      
        
          
            
          
          <div id="result"></div>
            
          
          
            
              
            
            
              
          
        
      {{-- kanan --}}
      {{-- 
        
          
            
              
                
                  
              
              
                
                  
              
            
              
              </div>
              <div class="tab-pane fade" id="ulasan" role="tabpanel" aria-labelledby="ulasan-tab">
              </div>
            </div>
          </nav>
        </div>
      </div> --}}
  </div>
@endsection

@section('script')
  <script>
    $(document).ready(function(){
      getData();
    })

    function GetProvinsi(prov){
      var URL = '{!! url("/api/rajaongkir/provinsi") !!}';
      var result = '';
      $.ajax({
        url: URL,
        type: 'get',
        data: {
          'id': prov
        },
        dataType: 'json',
        async: false,
        success: function(data){
          result = data.rajaongkir.results.province;
        }
      })
      return result;
    }

    function GetKota(kota){
      var URL = '{!! url("/api/rajaongkir/city") !!}';
      var result = '';
      $.ajax({
        url: URL,
        type: 'get',
        data: {
          'id': kota
        },
        dataType: 'json',
        async: false,
        success: function(data){
          result = data.rajaongkir.results.city_name;
        }
      })
      return result;
    }

    function getData(){
      var slug  = '{!! Request::segment(2) !!}';
      var base  = '{!! url("/api/toko") !!}';
      var URL   = base + '/' + slug;
      var key   = 'pandudpn09';

      $.ajax({
        url: URL,
        type: 'GET',
        dataType: 'json',
        success: function(data){
          var toko    = data.data.toko;
          var assets  = '{!! url("/assets/images/") !!}';
          var fotoToko= '';
          var provinsi= GetProvinsi(toko.provinsi);
          var kota    = GetKota(toko.kota);

          if(toko.foto_toko === ''){
            fotoToko  += 'toko.png';
          }else{
            fotoToko  += toko.foto_toko
          }

          var tUrl    = assets + '/toko/' + fotoToko;


          var html = '<div class="row mt-5">'; // row awal
              html += '<div class="col-md-3">'; // col-md-3 left side
              html += '<div class="_toko-left">'; // _toko-left
              html += '<div class="_cover-image d-block mx-auto">'; // _cover-image
              html += '<img src="' + tUrl + '" />';
              html += '</div>'; // end _cover-image
              html += '<div class="_cover-nama-toko d-block mx-auto mt-4">'; // _cover-nama-toko
              html += '<p class="_toko-nama">' + toko.nama_toko + '</p>';
              html += '</div>'; // end _cover-nama-toko
              html += '<div class="row mt-3">'; // row location and sales
              html += '<div class="col-md-6">'; // col-md-6 location
              html += '<p><i class="mdi mdi-map-marker"></i> Location</p>'; // location text
              html += '</div>'; // end location text
              html += '<div class="col-md-6 text-right">';
              html += '<p>' + kota + ', ' + provinsi + '</p>';
              html += '</div>'; // end col-md-6 right side
              html += '<hr>';
              html += '<div class="col-md-6">'; // col-md-6 location
              html += '<p><i class="mdi mdi-package-variant"></i> Total Penjualan</p>'; // sales text
              html += '</div>'; // end sales text
              html += '<div class="col-md-6 text-right">';
              html += '<p>' + new Intl.NumberFormat(['bal', 'id']).format(toko.totalPenjualan) + '</p>';
              html += '</div>'; // end col-md-6 right side
              html += '</div>'; // end row location and sales
              html += '</div>'; // end _toko-left
              html += '</div>'; // end left-side

              // right side
              html += '<div class="col-md-9">'; // col-md-9 right side
              html += '<div class="_toko-right">'; // _toko-right
              html += '<nav>'; // nav
              html += '<ul class="nav nav-tabs" id="myTab" role="tablist">'; // nav-tabs
              html += '<li class="nav-item _toko">'; // nav-item first
              html += '<a class="nav-link active" id="produk-tab" data-toggle="tab" href="#produk" role="tab" aria-controls="produk" aria-selected="true"><i class="mdi mdi-package"></i> Produk</a>'; // tabs produk
              html += '</li>'; // end nav-item first
              html += '<li class="nav-item _toko">'; // nav-item second (ulasan)
              html += '<a class="nav-link" id="ulasan-tab" data-toggle="tab" href="#ulasan" role="tab" aria-controls="ulasan" aria-selected="true"><i class="mdi mdi-comment-check-outline"></i> Ulasan</a>'; // tabs ulasan
              html += '</li></ul>'; // end nav-item second and end nav-tabs
              html += '<div class="tab-content" id="myTabContent">'; // tab-content
              html += '<div class="tab-pane fade show active" id="produk" role="tabpanel" aria-labelledby="produk-tab">'; // product tabs content
              
              if(data.data.produk.length > 0){
                $.map(data.data.produk, (produk, index) => {
                  console.log(produk);
                  var ec      = produk.id_produk.toString();
                  var encrypt = CryptoJS.AES.encrypt(ec, key) // end encryption

                  var baseUrl = '{!! url('/p') !!}'
                  var url     = baseUrl + '/' + produk.slug_produk + '/' + btoa(encrypt) // end url details

                  var imageUrl= assets + '/product/' + produk.nama_foto;

                  var rate    = '';
                  if(produk.totalRating === null){
                      rate    = 0;
                  }else{
                      rate    = produk.totalRating;
                  }

                  html += '<div class="columns">'
                  html += '<a href="' + url + '">'
                  html += '<div class="card">'
                  html += '<div class="cover-image">'
                  html += '<img class="card-img-top" src="' + imageUrl + '" />'
                  html += '</div>'
                  html += '<div class="card-body">' // card-body
                  html += '<h6 class="card-title">' + produk.nama_produk + '</h6>'
                  html += '<div class="row">' // row
                  html += '<div class="col-md-6">'
                  html += '<p class="card-price">Rp ' + new Intl.NumberFormat(['bal', 'id']).format(produk.harga) + '</p>'
                  html += '</div>'
                  html += '<div class="col-md-6">'
                  html += '<input id="input-'+ index + '" name="input-'+index+'" class="rating rating-loading" value="' + rate + '" data-size="xs" />'
                  html += '</div>'
                  html += '</div>' // end row
                  html += '</div>' // card-body
                  html += '</div></a></div>' // column, a, card
                })
              }

              html += '</div>'; // end product tabs content
              html += '</div>'; // end row awal

          $('#result').html(html);
          $('.rating').rating({
            showCaption: false,
            displayOnly: true
          })
        }
      })
    }
  </script>
@endsection