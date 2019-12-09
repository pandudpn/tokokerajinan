@extends('layouts.frontend')
@section('content')
    <div class="content-wrapper">
        <div class="col-md-12 mb-5 text-center mx-auto">
            <img src="{{ asset('/assets/images/logo.png') }}">
        </div>
        <div class="row">
            <div class="col-md-4 mx-auto d-block">
                <div class="_rl-cover-image">
                    <img src="{{ asset('/assets/images/bg-rl2.png') }}">
                </div>
                <div class="_rl-cover-text text-center">
                    <p><span class="judul">Toko Kerajinan</span> yang lengkap dan tepercaya.</p>
                </div>
            </div>
            <div class="col-md-4 mr-auto">
                @if (session('alert'))
                    <div class="alert alert-danger">
                        <h5 class="text-center">{{ session('alert') }}</h5>
                    </div>
                @endif
                <div class="_activation">
                    <div class="col-md-12 mb-3 text-center">
                        <h4>Masuk</h4>
                        <small>Untuk memulai belanja</small>
                    </div>
                    {!! Form::open(['class' => 'needs-validation', 'id' => 'formLogin', 'novalidate']) !!}
                    <div class="form-group">
                        <label for="username" class="col-form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" autofocus required>
                        <div class="invalid-feedback">
                            Silahkan masukan username Anda.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
                        <div class="invalid-feedback">
                            Silahkan masukan password anda.
                        </div>
                    </div>
                    <button class="btn btn-outline-orange btn-block" onmouseover="this.style.background='#ff914d'" onmouseout="this.style.background='#fff'" style="background: #fff; font-weight: bold; letter-spacing: 2px;" type="submit">Masuk</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
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
    </script>
        
@endsection