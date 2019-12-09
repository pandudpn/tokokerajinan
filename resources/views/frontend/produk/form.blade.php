@extends('layouts.frontend')
@section('content')
<div class="container">
  <div class="col-md-8 mx-auto">
    <div class="_create-toko">
      <div class="_cover-create-toko d-block mx-auto mb-5">
        <img src="{{ asset('/assets/images/new-product.png') }}" />
      </div>
      {!! Form::open(['files' => true, 'id' => 'formProduk', 'class' => 'form-horizontal needs-validation', 'novalidate']) !!}
      <input type="hidden" id="toko" name="toko" value="{{ session('toko') }}">
      <div class="form-group row">
        <label for="nama" class="col-md-4 col-form-label">Nama Produk / Barang</label>
        <div class="col-md-8">
          <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Produk / Barang Baru" required="required">
          <div class="invalid-feedback">
            Masukan nama produk / barang yang ingin dijual.
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label for="kategori" class="col-form-label col-md-4">Kategori</label>
        <div class="col-md-8">
          <select name="kategori" id="kategori" class="form-control" required="required">
            <option value="" selected disabled>--- PILIH KATEGORI ---</option>
          </select>
          <div class="invalid-feedback">
            Masukan Kategori Produk.
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label for="harga" class="col-form-label col-md-4">Harga</label>
        <div class="col-md-8">
          <input type="text" class="form-control divi" id="harga" name="harga" placeholder="Harga produk / barang yang dijual." required="required">
          <div class="invalid-feedback">
            Masukan harga <small>(satuan)</small> produk / barang yang ingin dijual.
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label for="stok" class="col-form-label col-md-4">Stok</label>
        <div class="col-md-8">
          <input type="text" class="form-control divi" id="stok" name="stok" placeholder="Stok produk / barang yang tersedia." required="required">
          <div class="invalid-feedback">
            Masukan stok produk / barang yang tersedia.
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label for="berat" class="col-form-label col-md-4">Berat</label>
        <div class="col-md-8">
          <input type="text" class="form-control divi" id="berat" name="berat" placeholder="Total berat (gram) Produk / Barang" required="required">
          <div class="invalid-feedback">
            Masukan total berat <small>(gram)</small> Produk / Barang.
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label for="desc" class="col-form-label col-md-4">Deskripsi Produk</label>
        <div class="col-md-8">
          <textarea name="desc" id="desc" cols="5" rows="5" class="form-control" placeholder="Deskripsi Produk" required></textarea>
          <div class="invalid-feedback">
            Masukan Deskripsi produk.
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label for="foto" class="col-form-label col-md-4">Foto Produk / Barang</label>
        <div class="col-md-8 result-foto">
          <div class="row">
            <div class="col-md-10">
              <input type="file" class="form-control mb-2 foto" id="foto" name="foto[]" required>
            </div>
            <div class="col-md-2 result-button">
              <a href="#" class="text-success tambahProduk"><i class="mdi mdi-plus mdi-24px"></i></a>
            </div>
          </div>
        </div>
        <div class="invalid-feedback">
          Masukan foto produk.
        </div>
      </div>
      <button class="btn btn-primary btn-block mt-3 mb-3" type="submit" id="buat" disabled="disabled">Tambahkan Produk</button>
      <div id="result_create"></div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
@endsection

@section('script')
  <script>
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();

    $(document).ready(function(){

      $('.divi').divide({
        delimiter: '.'
      });

      $('#formProduk').submit(function(e){
        e.preventDefault();

        newProduk();
      })

      $('.foto').change(function(e){
        e.preventDefault();

        $('#buat').removeAttr('disabled');
      })

      $('.tambahProduk').click(function(e){
        e.preventDefault();

        tambah();
      })

      kategori();

    });

    $(document).on('click', '.delete', function(e){
      e.preventDefault();

      var id = $(this).attr('id');
      $('.baris_'+id).remove();
    });

    function kategori(){
      $.ajax({
        url: '{{ url("/api/p/kategori") }}',
        type: 'get',
        dataType: 'json',
        success: function(data){
          var html = '';
          if(data.status === 200){
            $.map(data.data, (result, index) => {
              html += '<option value="' + result.id + '">' + result.nama_kategori + '</option>'
            });
            $('#kategori').append(html);
          }
        }
      })
    }

    function tambah(){
      var length = $('.result-foto').find('.row').length;
      var html = '<div class="row baris_' + length + '">';
          html += '<div class="col-md-10">';
          html += '<input type="file" class="form-control mb-2 foto" id="foto" name="foto[]" required>';
          html += '</div>';
          html += '<div class="col-md-2">';
          html += '<a href="#" class="text-danger delete" id="' + length + '"><i class="mdi mdi-close mdi-24px"></i></a>';
          html += '</div>';
          html += '</div>';
      $('.result-foto').append(html);
    }

    function newProduk(){
      var formData = new FormData($('#formProduk')[0]);
      var URL   = '{!! url("/api/p/baru") !!}';

      $.ajax({
        url: URL,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function(){
          $('#buat').html('<i class="mdi mdi-loading mdi-spin"></i>');
          $('#buat').attr('disabled', 'disabled');
        },
        success: function(result){
          var html = '';
          if(result.status === 201){
            html += '<div class="col-md-12 text-center"><i class="mdi mdi-check-outline mdi-48px text-success"></i></div>';
            html += '<p class="text-center">' + result.pesan + '</p>';
            $('._create-toko').html(html);
          }else{ 
            if(result.status === 400){
              html += '<small class="text-danger">' + result.pesan + '</small>'
            }else if(result.status === 406){
              html += '<small class="text-danger">' + result.pesan + '</small>'
            }
            $('#result_create').html(html).fadeIn('fast').show().delay(5000).fadeOut('slow');
          }
          $('#buat').html('Tambahkan Produk');
          $('#buat').removeAttr('disabled');
	  setTimeout(() => {
	    window.location.href = '{{ url("/toko/produk") }}';
	  }, 5000);
        },
        error: function(){
          $('#buat').html('Tambahkan Produk');
          $('#buat').removeAttr('disabled');
        }
      }) 
    }
  </script>
@endsection
