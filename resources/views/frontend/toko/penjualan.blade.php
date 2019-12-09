@extends('layouts.frontend')
@section('content')
  <div class="content-wrapper">
    <nav>
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item _toko">
          <a class="nav-link active" id="penjualan-tab" data-toggle="tab" href="#penjualan" role="tab" aria-controls="penjualan" aria-selected="true">
            <i class="mdi mdi-package-variant"></i> Penjualan
          </a>
        </li>
        <li class="nav-item _toko">
          <a class="nav-link" id="laporan-tab" data-toggle="tab" href="#laporan" role="tab" aria-controls="laporan" aria-selected="false">
            <i class="mdi mdi-chart-line"></i> Laporan Penjualan
          </a>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        {{-- penjualan --}}
        <div class="tab-pane fade show active" id="penjualan" role="tabpanel" aria-labelledby="penjualan-tab">
          @if (count($t) > 0)
            @foreach ($t as $data)
              <div class="_box-pembelian mb-5">
                <div class="_box-nopesanan d-block mx-auto">
                  <p class="text-center"><i class="mdi mdi-cart-outline pr-1"></i> No Pesanan <span>{{ $data->no_pesanan }}</span></p>
                </div>
                <div class="row mt-3 mb-3">
                  <div class="col-md-4 text-center">
                    Nama Pembeli : <b>{{ $data->pembeli }}</b>
                  </div>
                  <div class="col-md-4 text-center">
                    No Telepon : <b>{{ $data->no_telp }}</b>
                  </div>
                  <div class="col-md-4 text-center">
                    Email : <b>{{ $data->email }}</b>
                  </div>
                </div>
                @foreach ($toko as $result)
                  <?php
                  if($result->status_pengiriman == 2){
                    $status = 'Sudah melakukan pembayaran dan belum di proses.';
                    $class  = 'badge-danger';
                  }else if($result->status_pengiriman == 3){
                    $status = 'Sedang dalam pengiriman';
                    $class  = 'badge-primary';
                  }else if($result->status_pengiriman == 4){
                    $status = 'Barang telah tiba dan penjualan selesai.';
                    $class  = 'badge-success';
                  }
                  ?>
                  @if ($result->no_pesanan == $data->no_pesanan)
                    <div class="col-12 mb-3">
                      <div class="_box-pembelian-produk">
                        <div class="row">
                          <div class="col-12 col-md-2">
                            <div class="_box-cover-foto">
                              <img src="{{ asset('/assets/images/product/'.$result->nama_foto) }}">
                            </div>
                          </div>
                          <div class="col-6 col-md-3">
                            <div class="_pembelian-product-name">
                              <b>{{ $result->nama_produk }}</b>
                            </div>
                            <p>Jumlah : <b>{{ $result->jumlah }}</b></p>
                          </div>
                          <div class="col-6 col-md-4">
                            <div class="_pembelian-product-name">
                              Alamat Pembeli
                              <p class="_alamat-pembeli" data-kota="{{ $result->destination }}" data-alamat="{{ $result->alamat }}"></p>
                            </div>
                            <p>Status <span class="badge badge-pill {{ $class }}">{{ $status }}</span></p>
                          </div>
                          <div class="col-6 col-md-3">
                            @if ($result->status_pengiriman == 2)
                              <div class="pt-4">
                                <a href="{{ url('/toko/resi/'.encrypt($result->produk_id).'/'.encrypt($result->no_pesanan)) }}" title="Input nomer resi dan ubah status menjadi pengiriman" class="btn btn-outline-orange noresi"><i class="mdi mdi-square-edit-outline"></i> Input Nomer Resi</a>
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>
            @endforeach
            {!! $t->links() !!}
          @else
            <div class="col-md-12 mt-4 text-center">
              <h3 class="text-danger"><i>Belum ada penjualan.</i></h3>
            </div>
          @endif
        </div>

        {{-- laporan penjualan --}}
        <div class="tab-pane fade" id="laporan" role="tabpanel" aria-labelledby="laporan-tab">
          <div class="col-md-8 mx-auto mt-4">
            {!! Form::open(['id' => 'formPencarian', 'class' => 'form-horizontal needs-validation', 'novalidate', 'method' => 'get']) !!}
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="dari" class="col-form-label col-md-4">Dari</label>
                  <div class="col-md-8">
                    <input type="text" class="form-control" id="dari" name="f" placeholder="Dari Tanggal" required="required">
                    <div class="invalid-feedback">Tanggal awal harus di isi.</div>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="sampai" class="col-form-label col-md-4">Sampai</label>
                  <div class="col-md-8">
                    <input type="text" class="form-control" id="sampai" name="t" placeholder="Sampai Tanggal" required="required">
                    <div class="invalid-feedback">Tanggal akhir harus di isi.</div>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <button class="btn btn-outline-primary" type="submit">Cari</button>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
          <div class="mt-5">
            <canvas id="chartPenjualan" height="300" width="1000"></canvas>
          </div>
        </div>
      </div>
    </nav>
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

      chart();

      $('#dari').datetimepicker({
        format: 'Y-m-d',
        timepicker: false
      });

      $('#sampai').datetimepicker({
        format: 'Y-m-d',
        timepicker: false
      });

      $('._alamat-pembeli').each(function(index){
        var ele   = $(this);
        var kota  = $(this).data('kota');
        var alam  = $(this).data('alamat');

        GetKota(ele, kota, alam);
      });

      $('#formPencarian').submit(function(e){
        e.preventDefault();

        var dari  = $('#dari').val();
        var samp  = $('#sampai').val();

        chart(dari, samp);
      })

    });

    function GetKota(ele, kota, alam){
      var URL = '{!! url("/api/rajaongkir/city") !!}';

      $.ajax({
        url: URL,
        type: 'get',
        data: {
          'id': kota
        },
        dataType: 'json',
        cache: false,
        success: function(result){
          ele.html('<b>' + alam + ', ' + result.rajaongkir.results.city_name + ', ' + result.rajaongkir.results.province + ', ' + result.rajaongkir.results.postal_code + '</b>')
        }
      })
    }

    function chart(from, to){
      var URL;
      var uang = new Array();
      var waktu = new Array();

      if(from === undefined && to === undefined){
        URL = '{!! url("/toko/penjualan/chart") !!}';
      }else{
        URL = '{!! url("/toko/penjualan/chart") !!}?f=' + from + '&t=' + to;
      }

      $.get(URL, function(response){
        response.forEach(function(data){
          uang.push(data.total);
          waktu.push(data.bulan);
        });

        var ctx = document.getElementById('chartPenjualan').getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'line',
            data: {
              labels:waktu,
              datasets: [{
                label: 'Pendapatan',
                data: uang,
                backgroundColor: [
                  'rgba(0,0,0,0)'
                ],
                pointHoverBackgroundColor: 'rgba(255,0,0,1)',
                pointHoverBorderColor: 'rgba(255,0,0,1)',
                fill: false,
                borderColor: [
                  'rgba(255,0,0, 1)'
                ],
                borderWidth: 2
              }]
            },
            options: {
              scales: {
                yAxes: [{
                  ticks: {
                    beginAtZero:true
                  }
                }]
              },
              tooltips: {
                callbacks: {
                  label: function(tooltipItem, data) {
                    return 'Pendapatan : Rp ' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                  }
                }
              }
            }
        });
      });
    }

    $(document).on('click', '.noresi', function(e){
      e.preventDefault();

      var url = $(this).attr('href');
      var html = '<form method="post" class="form-horizontal" action="' + url + '">';
      html += '{{ csrf_field() }}';
      html += '<div class="form-group">'; // form group 1
      html += '<label for="isi">Nomer Resi</label>'; // label komentar
      html += '<input type="text" class="form-control" required="required" name="noresi" id="noresi" placeholder="Masukan Nomer Resi" />';
      html += '<div class="invalid-feedback">Isi nomer resi.</div>';
      html += '</div>'; // end form group 1
      html += '<button type="submit" class="btn btn-primary float-right">Simpan Nomer Resi</button>';
      html += '</form>';

      $('#ModalHeader').html('Nomer Resi');
      $('#ModalContent').html(html);
      $('#ModalFooter').html('<button type="button" data-dismiss="modal" class="btn btn-default">Batal</button>');
      $('#ModalGue').modal('show');
    });
  </script>
@endsection