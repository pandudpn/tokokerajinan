@extends('layouts.frontend')
@section('content')
  <div class="container">
    <div class="col-md-8 mx-auto">
      <div class="_create-toko">
        <div class="_cover-create-toko d-block mx-auto">
          <img src="{{ asset('/assets/images/create-toko.png') }}" />
        </div>
        {!! Form::open(['files' => true, 'id' => 'formCreate', 'class' => 'form-horizontal needs-validation', 'novalidate']) !!}
        <input type="hidden" id="user" name="user" value="{{ session('id') }}">
        <div class="form-row">
          <div class="col-md-6 mb-3">
            <label for="nama">Nama Toko</label>
            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Toko" required="required">
            <div id="cek_namatoko"></div>
            <div class="invalid-feedback">
              Masukan nama toko yang ingin dibuat.
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="provinsi">Provinsi</label>
            <select name="provinsi" id="provinsi" class="form-control" required="required">
              <option value="" disabled selected>-</option>
            </select>
            <div class="invalid-feedback">
              Pilih lokasi provinsi toko Anda.
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 mb-3">
            <label for="kota">Kota</label>
            <select name="kota" id="kota" class="form-control" required="required">
              <option value="" selected disabled>-</option>
            </select>
            <div class="invalid-feedback">
              Pilih lokasi kota toko Anda.
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="foto">Foto</label>
            <input type="file" class="form-control" name="foto" id="foto" required="required">
            <div class="invalid-feedback">
              Masukan foto toko Anda.
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 mb-3">
            <label for="alamat">Alamat Toko</label>
            <textarea name="alamat" id="alamat" cols="5" rows="5" class="form-control" placeholder="Alamat Toko" required="required"></textarea>
            <div class="invalid-feedback">
              Masukan Alamat toko Anda.
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="desc">Deskripsi Toko</label>
            <textarea name="desc" id="desc" cols="5" rows="5" class="form-control" placeholder="Deskripsi Toko" required="required"></textarea>
            <div class="invalid-feedback">
              Masukan Deskripsi toko Anda.
            </div>
          </div>
        </div>
        <button class="btn btn-primary btn-block mt-3 mb-3" type="submit" id="buat" disabled="disabled">Buat toko</button>
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
        GetProvinsi();

        $('#provinsi').change(function(e){
          e.preventDefault();
          var provinsi  = $(this).val();

          GetKota(provinsi);
        });

        $('#formCreate').submit(function(e){
          e.preventDefault();

          createToko();
        })

        $('#nama').change(function(e){
          e.preventDefault();
          var nama  = $(this).val()

          CekToko(nama);
        })

        $('#alamat').change(function(e){
          e.preventDefault();

          $('#buat').removeAttr('disabled');
        })

      });

      function CekToko(nama){
        var URL = '{!! url("/api/toko/baru/cek_toko") !!}'
        $.ajax({
          url: URL,
          type: 'get',
          data: {'nama' : nama},
          cache: false,
          dataType: 'json',
          beforeSend: function(){
            $('#cek_namatoko').html('<i class="mdi mdi-loading mdi-spin"></i>');
          },
          success: function(result){
            if(result.status === 200){
              $('#cek_namatoko').html('');
            }else if(result.status === 406){
              $('#cek_namatoko').html('<small class="text-danger">' + result.pesan + '</small>')
            }
          }
        })
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

            $('#provinsi').append(html);
          }
        });
      }

      function GetKota(provinsi){
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

            $('#kota').html(html);
          }
        });
      }

      function createToko(){
        var formData = new FormData($('#formCreate')[0]);
        var URL   = '{!! url("/api/toko/baru") !!}';

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
              html += '<p class="text-center">' + result.pesan + ' Silahkan login kembali untuk mengatifkan toko Anda.</p>';
              $('._create-toko').html(html);
            }else{ 
              if(result.status === 400){
                html += '<small class="text-danger">' + result.pesan + '</small>'
              }else if(result.status === 406){
                html += '<small class="text-danger">' + result.pesan + '</small>'
              }
              $('#result_create').html(html).fadeIn('fast').show().delay(5000).fadeOut('slow');
            }
            $('#buat').html('Buat toko');
            $('#buat').removeAttr('disabled');
          },
          error: function(){
            $('#buat').html('Buat toko');
            $('#buat').removeAttr('disabled');
          }
        }) 
      }
    </script>
@endsection