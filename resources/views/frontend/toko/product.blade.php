@extends('layouts.frontend')
@section('content')
  <div class="container">
    <div id="result"></div>
  </div>
@endsection

@section('script')
    <script>
      $(document).ready(function(){
        var toko  = '{{ session("toko") }}';
        GetData(toko);
      });

      $(document).on('click', '.hapusProduk', function(e){
        e.preventDefault();

        var Link      = $(this).attr('href');
        var splitUrl  = Link.split('/');
        var nama      = $(this).data('nama');

        var key     = 'pandudpn09';
        var id      = atob(splitUrl[5]);
        var d      = CryptoJS.AES.decrypt(id, key);
        var decrypt = d.toString(CryptoJS.enc.Utf8);
        var user    = splitUrl[6];

        $('#ModalHeader').html('Konfirmasi');
        $('#ModalContent').html('Apakah anda ingin menghapus produk / barang <b><i>' + nama + '</i></b>?');
        $('#ModalFooter').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button><button type="button" class="btn btn-danger" id="YesDelete" data-id="' + decrypt + '" data-user="' + atob(user) + '">Ya, saya yakin</button>');
        $('#ModalGue').modal('show');
      });

      $(document).on('click', '#YesDelete', function(e){
        e.preventDefault();
        var base = '{!! url("/api/p/delete") !!}';
        var URL  = base + '/' + $(this).data('id') + '/' + $(this).data('user');
        $.ajax({
          url: URL,
          type: 'DELETE',
          dataType: 'json',
          beforeSend: function(){
            $(this).addClass('disabled');
            $(this).html('<i class="mdi mdi-loading mdi-spin"></i>');
          },
          success: function(data){
            $('#ModalGue').modal('hide');
            $(this).removeClass('disabled');
            $(this).html('Ya, saya yakin');
            var toko  = '{{ session("toko") }}';
            GetData(toko);
          }
        })
      })

      function GetData(toko, produk){
        var URL   = '{!! url("/api/toko/produk/") !!}/' + toko;
        var key   = 'pandudpn09';

        $.ajax({
          url: URL,
          type: 'get',
          data: {
            'produk': produk
          },
          dataType: 'json',
          cache: false,
          success: function(data){
            var toko      = data.toko;
            var assets    = '{!! asset("/assets/images/") !!}';
            var fotoToko  = assets + "/toko/" + data.toko.foto_toko;

            var html    = '';
            // toko
            html += '<div class="_product-toko">'; // _product-toko
            html += '<div class="row">'; // row
            html += '<div class="col-md-2">'; // col-md-2
            html += '<div class="_product-foto-toko d-block mx-auto">'; // _product-foto-toko
            html += '<img src="' + fotoToko + '" />'; // foto-toko
            html += '</div></div>'; // end _product-foto-toko and end col-md-2
            html += '<div class="col-md-10 _product-right-side">'; // _product-right-side
            html += '<div class="row">'; // row
            html += '<div class="col-md-12">'; // col-md-12
            html += '<h6 class="_product-nama-toko">' + toko.nama_toko + '</h6>';
            html += '</div>'; // end col-md-12
            html += '<div class="col-md-12">';
            html += '<p class="_product-desc-toko">' + toko.desc_toko + '</p>'; // description toko
            html += '</div>'; // end col-md-12
            html += '</div></div></div>'; // end row and end _product-right-side and end row

            html += '<div class="row mt-5">';
            html += '<div class="col-md-2">';
            html += '<a href={{ url("/p/tambah") }} class="btn btn-outline-orange"><i class="mdi mdi-plus"></i> Tambah Produk</a>'; // new product
            html += '</div>'; // end col-md-2 mr-auto
            // if(data.produk.length > 0){
            //   html += '<div class="col-md-3 ml-auto">';
            //   html += '<div class="form-group row">'; // form-row
            //   html += '<label for="search" class="col-form-label col-md-2"><i class="mdi mdi-magnify mdi-24px"></i></label>'; // label
            //   html += '<div class="col-md-10">';
            //   html += '<input type="text" id="search" class="form-control" name="produk" placeholder="Cari produk....">';
            //   html += '</div></div>'; // end col-md-8 and end form-row
            //   html += '</div>'; // end col-md-3 ml-auto
            // }
            html += '</div>'; // end row

            // product
            html += '<div class="row">'; // row
            if(data.produk.length > 0){ // checking if there's data greater than 0
              $.map(data.produk, (result, index) => { // loop
              var session= '{{ session("id") }}';
              var status = '';
              if(result.status === 1){
                status += '<span class="badge badge-warning">Menunggu Konfirmasi</span>';
              }else if(result.status === 2){
                status += '<span class="badge badge-success">Produk Tersedia</span>';
              }else if(result.status === 3){
                status += '<span class="badge badge-danger">Produk tidak di jual.</span>';
              }

              var fotoProduct = assets + '/product/' + result.nama_foto; // url assets for product

              var ec      = result.id.toString();
              var encrypt = CryptoJS.AES.encrypt(ec, key) // end encryption

              // url details
              var baseUrl = '{!! url('/p') !!}'
              var url     = baseUrl + '/' + result.slug_produk + '/' + btoa(encrypt) // end url details
              var editUrl = baseUrl + '/ubah/' + btoa(encrypt) + '/' + btoa(session);
              var hapusUrl= baseUrl + '/hapus/' + btoa(encrypt) + '/' + btoa(session);

              html += '<div class="col-md-6">'; // col-md-6
              html += '<div class="_product-data">'; // _product-data'
              html += '<div class="row">'; // row
              html += '<div class="col-md-2">'; // col-md-2
              html += '<div class="_product-cover d-block mx-auto">'; // _product-cover for photo
              html += '<img src="' + fotoProduct + '" />'; // photo product
              html += '</div></div>'; // end _product-cover and end col-md-2
              html += '<div class="col-md-10">'; // col-md-10
              html += '<div class="row">'; // row
              html += '<div class="col-md-9">'; // col-md-9 before button side
              html += '<div class="row">'; // row
              html += '<div class="col-md-12">'; // col-md-12
              html += '<div class="_cover-nama-product">'; // _cover-nama-product
              html += '<a href="' + url + '" class="_product-nama">' + result.nama_produk + '</a>' + status;
              html += '</div></div>'; // end _cover-nama-product and col-md-12
              html += '<div class="col-md-5">'; // col-md-4
              html += '<small class="_product-harga" title="Harga Produk"><i class="mdi mdi-cash"></i> Rp ' + new Intl.NumberFormat(['bal', 'id']).format(result.harga) + '</small>'; // harga
              html += '</div>'; // end col-md-4
              html += '<div class="col-md-3">'; // col-md-4
              html += '<small class="_product-stok" title="Stok Produk"><i class="mdi mdi-package-variant-closed"></i> ' + new Intl.NumberFormat(['bal', 'id']).format(result.stok) + '</small>'; // stok
              html += '</div>'; // end col-md-4
              html += '<div class="col-md-4">'; // col-md-4
              html += '<small class="_product-berat" title="Berat Produk"><i class="mdi mdi-weight-gram"></i> ' + new Intl.NumberFormat(['bal', 'id']).format(result.berat) + '</small>'; // berat
              html += '</div>'; // end col-md-4
              html += '</div></div>'; // end row and end col-md-9
              html += '<div class="col-md-3">'; // col-md-3 (button)
              html += '<div class="row">'; // row
              html += '<div class="col-md-12">'; // col-md-12
              html += '<a href="' + editUrl + '" class="btn btn-outline-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> Ubah</a>'; // ubah
              html += '</div>'; // end col-md-12
              html += '<div class="col-md-12 pt-2">'; // col-md-12
              html += '<a href="' + hapusUrl + '" class="btn btn-outline-danger hapusProduk btn-sm" data-nama="' + result.nama_produk + '"><i class="mdi mdi-trash-can-outline"></i> Hapus</a>'; // hapus
              html += '</div>'; // end col-md-12 pt-2
              html += '</div></div>'; // end row and end-col-md-3 (button)
              html += '</div></div>'; // end row and end col-md-10
              html += '</div></div></div>'; // end row and end _product-data and end-col-md-6
              }) // end loop
            }else { // if there's no data
              html += '<div class="col-md-12 text-center text-danger mt-3"><h3><i>Anda belum mempunyai produk.</i></h3></div>';
            }
            html += '</div>'; // end row

            $('#result').html(html);
          }
        })
      }
    </script>
@endsection