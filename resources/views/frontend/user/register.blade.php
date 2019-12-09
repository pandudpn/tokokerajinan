@extends('layouts.frontend')
@section('content')
    <div class="content-wrapper">
        <div class="col-md-12 mb-5 text-center mx-auto">
            <img src="{{ asset('/assets/images/logo.png') }}">
        </div>
        <div class="row">
            <div class="col-md-4 mx-auto _activation">
                <div class="col-md-12 mb-3 text-center">
                    <h4>Daftar</h4>
                    <small>Untuk memulai belanja atau berjualan.</small>
                </div>
                {!! Form::open(['id' => 'formRegister', 'class' => 'form-horizontal']) !!}
                <div class="form-group row">
                    <label for="nama" class="col-form-label col-md-4">Nama</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Kamu" required="required" autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="no_telp" class="col-form-label col-md-4">Nomer Telp</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="no_telp" name="no_telp" placeholder="Nomer Telepon" required="required">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-form-label col-md-4">Email</label>
                    <div class="col-md-8">
                        <input type="email" class="form-control" id="email" name="email" placeholder="email@email.com" required="required">
                        <div id="cek_email"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="username" class="col-form-label col-md-4">Username</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required="required">
                        <div id="cek_username"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label">Password</label>
                    <div class="col-md-8">
                        <input type="password" class="form-control" id="password" name="password" placeholder="**********" required="required">
                    </div>
                </div>
                <button class="btn btn-primary btn-block disabled" type="submit">Daftar</button>
                {!! Form::close() !!}
            </div>
            <div class="col-md-4 mr-auto">
                <div class="_rl-cover-image" style="height: 360px;">
                    <img src="{{ asset('/assets/images/bg-rl2.png') }}">
                </div>
                <div class="_rl-cover-text text-center">
                    <p><span class="judul">Toko Kerajinan</span> yang lengkap dan tepercaya.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function(){
            $('#email').change(function(e){
                e.preventDefault();
                var email   = $(this).val();
                $.ajax({
                    url: '{{ url("/api/user/cek_email") }}',
                    type: 'get',
                    data: {'email' : email},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){
                        $('#cek_email').html('<i class="mdi mdi-loading mdi-spin"></i>');
                    },
                    success: function(data){
                        setTimeout(() => {
                            if(data.status === 200){
                                $('#cek_email').html('');
                            }else if(data.status === 406){
                                $('#cek_email').html('<small class="text-danger">' + data.pesan + '</small>');
                            }
                        }, 300);
                    }
                })
            })

            $('#username').change(function(e){
                e.preventDefault();
                var username   = $(this).val();
                $.ajax({
                    url: '{{ url("/api/user/cek_username") }}',
                    type: 'get',
                    data: {'username' : username},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){
                        $('#cek_username').html('<i class="mdi mdi-loading mdi-spin"></i>');
                    },
                    success: function(data){
                        setTimeout(() => {
                            if(data.status === 200){
                                $('#cek_username').html('');
                                $('[type="submit"]').removeClass('disabled')
                            }else if(data.status === 406){
                                $('#cek_username').html('<small class="text-danger">' + data.pesan + '</small>');
                            }
                        }, 300);
                    }
                })
            });

            $('#formRegister').submit((e) => {
                e.preventDefault();
                var nama    = $('#nama').val();
                var email   = $('#email').val();
                var telp    = $('#no_telp').val();
                var user    = $('#username').val();
                var pass    = $('#password').val();

                $.ajax({
                    url: '{{ url("/api/user/register") }}',
                    type: 'POST',
                    data: {
                        'nama' : nama,
                        'no_telp' : telp,
                        'email' : email,
                        'username' : user,
                        'password' : pass
                    },
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){
                        $('[type="submit"]').html('<i class="mdi mdi-loading mdi-spin"></i>');
                        $('[type="submit"]').addClass('disabled');
                    },
                    success: function(result){
                        var html = '';
                        setTimeout(() => {
                            $('[type="submit"]').html('Daftar');
                            $('[type="submit"]').removeClass('disabled');
                            // html += '<div class="_activation">';
                            html += '<div class="col-md-12 text-center text-warning">';
                            html += '<i class="mdi mdi-check mdi-48px"></i>';
                            html += '</div>';
                            html += '<p>' + result.pesan + '</p>';
                            // html += '</div>';

                            $('._activation').html(html).fadeIn('slow');
                        }, 300);
                    }
                })
            })
        });
    </script>
@endsection