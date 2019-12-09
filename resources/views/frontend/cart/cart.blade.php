@extends('layouts.frontend')
@section('content')
  <div class="content-wrapper mt-5">
    <div id="result"></div>
  </div>
@endsection
@section('script')
  <script>
    $(document).ready(function(){
      getData();
    });

    $(document).on('click', '.tambah', function(e){
      e.preventDefault();

      var id = $(this).data('id');

      $.ajax({
        url: $(this).attr('href'),
        type: 'POST',
        data: {
          '_token': '{{ csrf_token() }}',
          'id': id,
          'qty': 1,
          'tipe': 'tambah'
        },
        dataType: 'json',
        cache: false,
        success: function(data){
          getData();
        }
      })
    });

    $(document).on('click', '.kurang', function(e){
      e.preventDefault();

      var id = $(this).data('id');

      $.ajax({
        url: $(this).attr('href'),
        type: 'POST',
        data: {
          '_token': '{{ csrf_token() }}',
          'id': id,
          'qty': 1,
          'tipe': 'kurang'
        },
        dataType: 'json',
        cache: false,
        success: function(data){
          getData();
        }
      })
    });

    function getData(){
      var URL = '{!! url("/cart/getdata") !!}';

      $.ajax({
        url: URL,
        type: 'get',
        dataType: 'json',
        success: function(data){
          var t = 0;
          var html = '';
          var no = 1;
          var sto = 0;
          var untung = 0;

          if(data.status === 404){
            html += '<div class="col-md-12 mt-3 text-center mx-auto d-block"><h4 class="text-danger">Belum ada keranjang Belanja</h4></div>'
          }else if(data.status === 200){
            html += '<div class="row">'
            html += '<div class="col-md-1 text-center">No</div>'
            html += '<div class="col-md-4 text-center">Produk</div>'
            html += '<div class="col-md-1 text-center">Harga</div>'
            html += '<div class="col-md-3 text-center">Jumlah Barang</div>'
            html += '<div class="col-md-3 text-center">Sub Total</div>'
            html += '</div>'
            $.map(data.data, (result, index) => {
              var subtotal = total(result.qty, result.harga);
              t += subtotal + parseInt(result.ongkos);
              sto += +parseInt(result.ongkos);

              untung += +parseInt(result.qty);

              var base  = '{!! url("/assets/images/product") !!}';
              var image = base + '/' + result.foto

              html += '<div class="row mt-3 _bungkus">'
              html += '<div class="col-md-1">' // columns no
              html += '<div class="cover-cart text-center">' // cover-cart no
              html += '<p>' + no + '</p>' // nilai
              html += '</div>' // end cover-cart no
              html += '</div>' // end columns no
              html += '<div class="col-md-2">' // columns foto produk
              html += '<div class="cover-cart-foto">' // cover-cart-foto
              html += '<img src="' + image + '" />'
              html += '</div>' // end cover-cart-foto
              html += '</div>' // end columns foto produk
              html += '<div class="col-md-2">' // columns nama produk
              html += '<div class="cover-cart">' // cover-cart name product
              html += '<p>' + result.nama + '</p>'
              html += '</div>' // end cover-cart name product
              html += '</div>' // end columns nama produk
              html += '<div class="col-md-1">' // columns harga
              html += '<div class="cover-cart text-center">' // cover-cart price
              html += '<p>Rp ' + new Intl.NumberFormat('id').format(result.harga) + '</p>'
              html += '</div>' // end cover-cart price
              html += '</div>' // end columns harga
              html += '<div class="col-md-3">' // columns qty
              html += '<div class="row qty">' // row 3
              html += '<div class="col-md-3 offset-md-2">' // button minus
              html += '<a href={{ url("/cart/update") }} class="btn btn-outline-warning kurang" data-id="' + index + '"><i class="mdi mdi-minus"></i></a>'
              html += '</div>' // end button minus
              html += '<div class="col-md-2">' + result.qty + '</div>'
              html += '<div class="col-md-3">' // button plus
              html += '<a href={{ url("/cart/update") }} class="btn btn-outline-warning tambah" data-id="' + index + '"><i class="mdi mdi-plus"></i></a>'
              html += '</div>' // end button plus
              html += '</div>' // end row 3
              html += '</div>' // end columns qty
              html += '<div class="col-md-3 text-center">' // columns subtotal
              html += '<div class="cover-cart">' // cover-cart name product
              html += '<p>Rp ' + new Intl.NumberFormat('id').format(subtotal) + '</p>'
              html += '</div>' // end cover-cart name product
              html += '</div>' // end subtotal

              // pengiriman
              html += '<div class="col-md-9 mt-3">' // columns pengiriman
              html += '<input type="hidden" id="asal_'+index+'" class="asal" value="'+result.kota+'" />'
              html += '<input type="hidden" id="berat_'+index+'" class="berat" value="'+result.berat+'" />'
              html += '<div class="row">' // row 4
              html += '<div class="col-md-2 pt-4 text-center">' // text
              html += '<h5><i>Pengiriman</i></h5>'
              html += '</div>' // end text
              html += '<div class="col-md-3">' // columns provinsi
              html += '<div class="form-group">' // form-group
              html += '<label for="provinsi_'+index+'">Provinsi</label>' // label provinsi
              html += '<select class="form-control provinsi" data-id="'+index+'" id="provinsi_'+index+'" name="provinsi">' // select prov
              html += '<option selected disabled>-</option>'
              html += '</select>' // end select prov
              html += '</div>' // end form-group
              html += '</div>' // end columns provinsi
              html += '<div class="col-md-3">' // columns kota
              html += '<div class="form-group">' // form-group
              html += '<label for="kota_'+index+'">Kota</label>' // label kota
              html += '<select class="form-control kota" data-id="'+index+'" id="kota_'+index+'" name="kota">' // select kota
              html += '<option selected disabled>-</option>'
              html += '</select>' // end select kota
              html += '</div>' // end form-group
              html += '</div>' // end columns kota
              html += '<div class="col-md-2">' // columns kurir
              html += '<div class="form-group">' // form-group
              html += '<label for="kurir">Kurir</label>' // label kurir
              html += '<select class="form-control kurir" data-id="'+index+'" id="kurir" name="kurir">' // select prov
              html += '<option value="jne">JNE</option>'
              html += '<option value="tiki">TIKI</option>'
              html += '<option value="pos">Pos Indonesia</option>'
              html += '</select>' // end select prov
              html += '</div>' // end form-group
              html += '</div>' // end columns kurir
              html += '<div class="col-md-2">' // columns service
              html += '<div class="form-group">' // form-group
              html += '<label for="service_'+index+'">Layanan</label>' // label service
              html += '<select class="form-control service" data-id="'+index+'" id="service_'+index+'" name="service">' // select prov
              html += '<option selected disabled price="0">-</option>'
              html += '</select>' // end select prov
              html += '</div>' // end form-group
              html += '</div>' // end columns service
              html += '</div>' // end row 4
              html += '</div>' // end columns pengiriman
              html += '<div class="col-md-3 text-center pt-4 _subtotal-pengiriman_'+index+'">' // columns subtotal pengiriman
              html += '<p>Rp ' + new Intl.NumberFormat('id').format(result.ongkos) + '</p>'
              html += '<input type="hidden" class="_ongkos_'+no+'" value="' + (result.ongkos === 0 ? "" : result.ongkos) + '" />'
              html += '</div>' // end columns subtotal pengiriman
              html += '</div>' // end _bungkus

              no++;
            });

            var totalUntung = (parseInt(untung) * 200) + parseInt(t);

            html += '<div class="row mt-5">'
            html += '<div class="col-md-9 text-right pr-5"><h6>Total Pengiriman</h6></div>'
            html += '<div class="col-md-3 text-center"><h6>Rp ' + new Intl.NumberFormat('id').format(sto) + '</h6></div>'
            html += '</div>';
            html += '<div class="row mt-5">'
            html += '<div class="col-md-9 text-right pr-5"><h4>Total Pembayaran</h4></div>'
            html += '<div class="col-md-3 text-center"><h4>Rp ' + new Intl.NumberFormat('id').format(totalUntung) + '</h4></div>'
            html += '</div>';
            html += '{!! Form::open(["url" => url("/pembayaran")]) !!}';
            html += '<input type="hidden" name="total_pembayaran" id="total_pembayaran" value="' + t + '" />';
            html += '@method("get")'
            html += '<div class="row mt-3">'
            html += '<div class="col-md-3 offset-md-9 text-center"><button class="btn btn-primary">Lanjut Pembayaran</button></div>'
            html += '</div>';
            html += '{!! Form::close() !!}'
          }

          $('#result').html(html);

          var length = '{{ count(session("cart")) }}';
          var oi = 0;
          for(var io = 1; io <= length; io++){
            var abb = $('._ongkos_'+io).val();

            if(abb === ''){
              oi++;
            }
          }

          if(oi > 0){
            $('.btn-primary').attr('disabled', 'disabled');
          }else{
            $('.btn-primary').removeAttr('disabled');
          }

          GetProvinsi();

          $('.provinsi').change(function(e){
            e.preventDefault();

            var prov  = $(this).val();
            var id    = $(this).data('id');
            GetKota(prov, id);
          })

          $('.kota').change(function(e){
            e.preventDefault();
            var asal    = $(this).parents('.col-md-9').find('.asal').val();
            var tujuan  = $(this).val();
            var kurir   = $(this).parents('.col-md-9').find('[name="kurir"]').val();
            var berat   = $(this).parents('.col-md-9').find('.berat').val();
            var id      = $(this).data('id');
            
            Cost(asal, tujuan, berat, kurir, id);
          });

          $('.kurir').change(function(e){
            e.preventDefault();

            var asal    = $(this).parents('.col-md-9').find('.asal').val();
            var tujuan  = $(this).parents('.col-md-9').find('.kota').val();
            var kurir   = $(this).val();
            var berat   = $(this).parents('.col-md-9').find('.berat').val();
            var id      = $(this).data('id');

            Cost(asal, tujuan, berat, kurir, id);
          })

          $('.service').change(function(e){
            e.preventDefault();

            var cost  = $(this).find('option:selected').attr('price');
            var id    = $(this).data('id');
            var tujuan= $('#kota_'+id).val();
            var kurir = $(this).parents('.col-md-9').find('.kurir').val();

            $('._subtotal-pengiriman_'+id).html('<p>Rp '+ new Intl.NumberFormat('id').format(cost) +'</p>')
            UpdateCost(cost, id, tujuan, kurir);
          })
        }
      })
    }

    

    function total(qty, harga){
      var to = 0;
      to  += +parseInt(qty) * parseInt(harga);
      return to;
    }

    function GetProvinsi(){
      var URL = '{!! url("/api/rajaongkir/provinsi") !!}';

      $.ajax({
        url: URL,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(result){
          var html = '';
          $.map(result.rajaongkir.results, (data, index) => {
            html += '<option value="' + data.province_id + '">' + data.province + '</option>'
          });
          $('.provinsi').append(html);
        }
      });
    }

    function GetKota(provinsi, id){
      var URL = '{!! url("/api/rajaongkir/city") !!}';

      $.ajax({
        url: URL,
        type: 'GET',
        dataType: 'json',
        data: {'provinsi' : provinsi},
        cache: false,
        success: function(result){
          var html = '';
          $.map(result.rajaongkir.results, (data, index) => {
            html += '<option value="' + data.city_id + '">' + data.type + ' - ' + data.city_name + '</option>'
          });

          $('#kota_'+id).html(html);
        }
      });
    }

    function Cost(asal, tujuan, berat, kurir, id){
      var URL = '{!! url("/api/rajaongkir/cost") !!}'
      $.ajax({
        url: URL,
        type: 'POST',
        data: {
          'kota_awal': asal,
          'kota_tujuan': tujuan,
          'berat': berat,
          'kurir': kurir
        },
        dataType: 'json',
        cache: false,
        success: function(result){
          var html = '';
          $('#service_'+id).find('option').empty();
          $.map(result.rajaongkir.results[0].costs, (data, index) => {
            html += '<option value="' + index + '" price="'+data.cost[0].value+'">' + data.service + ' (Rp ' + new Intl.NumberFormat('id').format(data.cost[0].value) + ')</option>'
          })
          $('#service_'+id).append('<option price="0" selected disabled>-</option>');
          $('#service_'+id).append(html);
          var c = $('#service_'+id+' :selected').attr('price');
          $('._subtotal-pengiriman_'+id).html('<p>Rp '+ new Intl.NumberFormat('id').format(c) +'</p>');
        }
      })
    }

    function UpdateCost(cost, id, tujuan, kurir){
      var URL = '{!! url("/cart/updatecost") !!}';

      $.ajax({
        url: URL,
        type: 'post',
        data: {
          '_token': '{{ csrf_token() }}',
          'id': id,
          'ongkos': cost,
          'kota_tujuan': tujuan,
          'kurir': kurir
        },
        dataType: 'json',
        cache: false,
        success: function(result){
          getData();
        }
      })
    }
  </script>
@endsection