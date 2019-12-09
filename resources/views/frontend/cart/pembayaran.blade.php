@extends('layouts.frontend')
@section('content')
  <div class="content-wrapper mt-4">
    <div id="stepperForm" class="bs-stepper mx-auto col-md-8">
      <div class="bs-stepper-header" role="tablist">
        <div class="step" data-target="#test-form-1">
          <button type="button" class="step-trigger" role="tab" id="stepperFormTrigger1" aria-controls="test-form-1">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label">Data Diri</span>
          </button>
        </div>
        <div class="bs-stepper-line"></div>
        <div class="step" data-target="#test-form-2">
          <button type="button" class="step-trigger" role="tab" id="stepperFormTrigger2" aria-controls="test-form-2">
            <span class="bs-stepper-circle">2</span>
            <span class="bs-stepper-label">Detail Pesanan</span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form class="needs-validation" id="formPembayaran" onsubmit="return submitForm();" novalidate>
          {{-- data diri --}}
          <div id="test-form-1" role="tabpanel" class="bs-stepper-pane fade" aria-labelledby="stepperFormTrigger1">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nama">Atas Nama <span class="text-danger font-weight-bold">*</span></label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Pesanan Atas Nama" value="{{ $user->nama }}" required="required">
                <input type="hidden" name="user_id" value="{{ session('id') }}" id="user_id">
                <div class="invalid-feedback">Silahkan isi atas nama pesanan.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="nama">Email <span class="text-danger font-weight-bold">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email@email.com" value="{{ $user->email }}" required="required">
                <div class="invalid-feedback">Silahkan isi email.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="alamat">Alamat <span class="text-danger font-weight-bold">*</span></label>
                <textarea name="alamat" id="alamat" cols="5" rows="5" class="form-control" placeholder="Alamat Rumah Lengkap" required="required"></textarea>
                <div class="invalid-feedback">Silahkan isi alamat rumah.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="notelp">No Telp <span class="text-danger font-weight-bold">*</span></label>
                <input type="text" class="form-control" id="notelp" name="notelp" placeholder="08xxxxxxxx" value="{{ $user->no_telp }}" required="required">
                <div class="invalid-feedback">Silahkan isi nomer telepon.</div>
              </div>
            </div>
            <button class="btn btn-primary btn-next-form" type="button">Next</button>
          </div>

          {{-- Details Pesanan --}}
          <div id="test-form-2" role="tabpanel" class="bs-stepper-pane fade" aria-labelledby="stepperFormTrigger2">
            <div class="row">
              <div class="col-12 mb-2">
                <h5><i class="mdi mdi-arrow-right-bold-circle"></i> Data Diri</h5>
              </div>
              <div class="col-6 mb-2">
                <div class="form-group">
                  <label for="nama">Atas Nama</label>
                  <p id="namaDetail"></p>
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="form-group">
                  <label for="email">Email</label>
                  <p id="emailDetail"></p>
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="form-group">
                  <label for="notelp">Nomer Telepon</label>
                  <p id="notelpDetail"></p>
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="form-group">
                  <label for="alamatDetail">Alamat</label>
                  <p id="alamatDetail"></p>
                </div>
              </div>
              <div class="col-12 mb-2 mt-1 border-top"></div>
              <div class="col-12 mb-2 mt-2">
                <h5><i class="mdi mdi-arrow-right-bold-circle"></i> Detail Pesanan</h5>
              </div>
            </div>
            <div class="row mt-4">
              <div class="col-1 mb-2">
                <center>No</center>
              </div>
              <div class="col-4 mb-2">
                <center>Produk</center>
              </div>
              <div class="col-2 mb-2">
                <center>Harga</center>
              </div>
              <div class="col-1 mb-2">
                <center>Jumlah</center>
              </div>
              <div class="col-2 mb-2">
                <center>Ongkos</center>
              </div>
              <div class="col-2 mb-2">
                <center>Sub Total</center>
              </div>
              <?php $no = 1;
              $total = 0;
              $untung = 0; ?>
              @foreach (session('cart') as $cart)
              @php
                $subtotal = (intval($cart['harga']) * intval($cart['qty'])) + intval($cart['ongkos']);
                $total    += $subtotal;
                $untung += intval($cart['qty']);
              @endphp
                <div class="col-1 mb-3">
                  <center>{{ $no++ }}</center>
                </div>
                <div class="col-4 mb-3">
                  <div class="row">
                    <div class="col-5">
                      <div class="_pembayaran-cover-foto">
                        <img src="{{ asset('/assets/images/product/'.$cart['foto']) }}">
                      </div>
                    </div>
                    <div class="col-7">
                      <b>{{ $cart['nama'] }}</b>
                    </div>
                  </div>
                </div>
                <div class="col-2 mb-3">
                  <center>{{ "Rp " .number_format($cart['harga'], 0, '.', '.') }}</center>
                </div>
                <div class="col-1 mb-3">
                  <center>{{ $cart['qty'] }}</center>
                </div>
                <div class="col-2 mb-3">
                  <center>{{ "Rp ".number_format($cart['ongkos'], 0, '.','.') }}</center>
                </div>
                <div class="col-2 mb-3">
                  <center>{{ "Rp ".number_format($subtotal, 0, '.','.') }}</center>
                </div>
              @endforeach
              <?php $totalUntung = (intval($untung) * 200) + intval($total); ?>
              <div class="col-12 border-top mt-2 mb-2"></div>
              <div class="col-10 mt-2 mb-4 text-right">
                <h5><i>Total</i></h5>
              </div>
              <div class="col-2 mt-2 mb-4">
                <center>{{ "Rp ".number_format($totalUntung, 0, '.', '.') }}</center>
              </div>
            </div>
            <button class="btn btn-primary" type="submit">Pembayaran</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script src="{{ !config('services.midtrans.isProduction') ? 'https://app.sandbox.midtrans.com/snap/snap.js' : 'https://app.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var stepperForm;
      var stepperFormEl = document.querySelector('#stepperForm');

      stepperForm = new Stepper(stepperFormEl, {
        animation: true
      });

      var btnNextList = [].slice.call(document.querySelectorAll('.btn-next-form'));
      var stepperPanList = [].slice.call(stepperFormEl.querySelectorAll('.bs-stepper-pane'));
      var email   = document.getElementById('email');
      var nama    = document.getElementById('nama');
      var alamat  = document.getElementById('alamat');
      var no_telp = document.getElementById('notelp');
      var form = stepperFormEl.querySelector('.bs-stepper-content form');

      btnNextList.forEach(function (btn) {
        btn.addEventListener('click', function () {
          stepperForm.next()
        });
      });

      stepperFormEl.addEventListener('show.bs-stepper', function (event) {
        form.classList.remove('was-validated');
        document.getElementById('namaDetail').innerHTML = '<b>' + nama.value + '</b>';
        document.getElementById('emailDetail').innerHTML = '<b>' + email.value + '</b>';
        document.getElementById('alamatDetail').innerHTML = '<b>' + alamat.value + '</b>';
        document.getElementById('notelpDetail').innerHTML = '<b>' + no_telp.value + '</b>';
        var nextStep = event.detail.indexStep;
        var currentStep = nextStep;

        if (currentStep > 0) {
          currentStep--;
        }

        var stepperPan = stepperPanList[currentStep];

        if ((stepperPan.getAttribute('id') === 'test-form-1' && (!nama.value.length || !email.value.length || !no_telp.value.length || !alamat.value.length))
        || (stepperPan.getAttribute('id') === 'test-form-2' && !inputPasswordForm.value.length)) {
          event.preventDefault();
          form.classList.add('was-validated');
        }
      });
    });

    function submitForm() {
      // Kirim request ajax
      $.post("{{ route('pesanan.store') }}",
      {
        _method: 'POST',
        _token: '{{ csrf_token() }}',
        user_id: $('#user_id').val(),
        nama: $('#nama').val(),
        email: $('#email').val(),
        notelp: $('#notelp').val(),
        alamat: $('#alamat').val()
      },
      function (data, status) {
        var URL = '{!! url("/") !!}';
        snap.pay(data.snap_token, {
          // Optional
          onSuccess: function (result) {
            window.location.href = URL + '/status/' + data.pesanan;
          },
          // Optional
          onPending: function (result) {
            window.location.href = URL + '/status/' + data.pesanan;
          },
          // Optional
          onError: function (result) {
            window.location.href = URL;
          }
        });
      });
      return false;
    }

  </script>
@endsection