@extends('layouts.frontend')

@section('content')
  <div class="content-wrapper">
    <nav>
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="pembelian-tab" data-toggle="tab" href="#pembelian" role="tab" aria-controls="pembelian" aria-selected="true">
            <i class="mdi mdi-cart-outline"></i> Pembelian
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="selesai-tab" data-toggle="tab" href="#selesai" role="tab" aria-controls="selesai" aria-selected="false">
            <i class="mdi mdi-truck-check"></i> Pembelian Selesai
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="laporan-tab" data-toggle="tab" href="#laporan" role="tab" aria-controls="laporan" aria-selected="false">
            <i class="mdi mdi-file-document-edit-outline"></i> Laporan Pembelian
          </a>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        {{-- pembelian --}}
        <div class="tab-pane fade show active" id="pembelian" role="tabpanel" aria-labelledby="pembelian-tab">
          @if (count($p) > 0)
            @foreach ($p as $data)
              <div class="_box-pembelian mb-5">
                <div class="_box-nopesanan d-block mx-auto">
                  <p class="text-center"><i class="mdi mdi-cart-outline pr-1"></i> No Pesanan <span>{{ $data->no_pesanan }}</span></p>
                </div>
                <div class="row">
                  @foreach ($pembelian as $result)
                    @if ($data->no_pesanan == $result->no_pesanan)
                      <?php
                      $total  = intval($result->jumlah_pesanan) * intval($result->harga);
                      if($result->status_pengiriman == 1){
                        $class  = 'badge-secondary';
                        $status = 'Belum Bayar';
                      }else if($result->status_pengiriman == 2){
                        $class  = 'badge-info';
                        $status = 'Sedang di proses';
                      }else if($result->status_pengiriman == 4){
                        $class  = 'badge-success';
                        $status = 'Selesai';
                      }else if($result->status_pengiriman == 5){
                        $class  = 'badge-danger';
                        $status = 'Batal';
                      }else if($result->status_pengiriman == 3){
                        $class  = 'badge-primary';
                        $status = 'Pengiriman';
                      }
                      ?>
                      <div class="col-12 col-md-6 mb-3">
                        <div class="_box-pembelian-produk">
                          <div class="row">
                            <div class="col-12 col-md-3">
                              <div class="_box-cover-foto">
                                <img src="{{ asset('/assets/images/product/'.$result->nama_foto) }}">
                              </div>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                <b>{{ $result->nama_produk }}</b>
                              </div>
                              <p>Jumlah : <b>{{ $result->jumlah_pesanan }}</b></p>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                Harga 
                                <p><b>{{ "Rp ".number_format($result->harga, 0, ',', '.') }}</b></p>
                              </div>
                              <p>Status <span class="badge badge-pill {{ $class }}">{{ $status }}</span></p>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                Total Harga <p><b>{{ "Rp ".number_format($total, 0, ',', '.') }}</b></p>
                              </div>
                              @if ($result->status_pengiriman == 3)
                                <a href="{{ url('/pembelian/konfirmasi/'.encrypt($result->no_pesanan).'/'.encrypt($result->produk_id)) }}" data-nama="{{ $result->nama_produk }}" class="btn btn-outline-orange konfirmasi">Konfirmasi</a>
                              @elseif ($result->status_pengiriman == 1)
                                <small class="tgl" data-end="{{ $result->created_at->addDays(3) }}"></small>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    @endif
                  @endforeach
                </div>
              </div>
            @endforeach
            {!! $p->links() !!}
          @else
            <div class="col-md-12 mt-4 text-center">
              <h3 class="text-danger"><i>Anda belum melakukan pembelian.</i></h3>
            </div>
          @endif
        </div>

        {{-- pembelian selesai --}}
        <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
          @if (count($sp) > 0)
            @foreach ($sp as $data)
              <div class="_box-pembelian mb-5">
                <div class="_box-nopesanan d-block mx-auto">
                  <p class="text-center"><i class="mdi mdi-cart-outline pr-1"></i> No Pesanan <span>{{ $data->no_pesanan }}</span></p>
                </div>
                <div class="row">
                  @foreach ($selesai as $result)
                    @if ($data->no_pesanan == $result->no_pesanan)
                      <?php
                      $total  = intval($result->jumlah_pesanan) * intval($result->harga);
                      if($result->status_pengiriman == 1){
                        $class  = 'badge-secondary';
                        $status = 'Belum Bayar';
                      }else if($result->status_pengiriman == 2){
                        $class  = 'badge-info';
                        $status = 'Sedang di proses';
                      }else if($result->status_pengiriman == 4){
                        $class  = 'badge-success';
                        $status = 'Selesai';
                      }else if($result->status_pengiriman == 5){
                        $class  = 'badge-danger';
                        $status = 'Batal';
                      }else if($result->status_pengiriman == 3){
                        $class  = 'badge-primary';
                        $status = 'Pengiriman';
                      }
                      ?>
                      <div class="col-12 col-md-6 mb-3">
                        <div class="_box-pembelian-produk">
                          <div class="row">
                            <div class="col-12 col-md-3">
                              <div class="_box-cover-foto">
                                <img src="{{ asset('/assets/images/product/'.$result->nama_foto) }}">
                              </div>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                <b>{{ $result->nama_produk }}</b>
                              </div>
                              <p>Jumlah : <b>{{ $result->jumlah_pesanan }}</b></p>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                Harga <p><b>{{ "Rp ".number_format($result->harga, 0, ',', '.') }}</b></p>
                              </div>
                              <p>Status <span class="badge badge-pill {{ $class }}">{{ $status }}</span></p>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                Total Harga <p><b>{{ "Rp ".number_format($total, 0, ',', '.') }}</b></p>
                              </div>
                              @if ($result->komentar_id == null)
                                <a href="{{ url('/pembelian/komentar/'.encrypt($result->no_pesanan).'/'.encrypt($result->produk_id)) }}" data-nama="{{ $result->nama_produk }}" class="btn btn-outline-orange ulasan">Ulasan</a>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    @endif
                  @endforeach
                </div>
              </div>
            @endforeach
            {!! $sp->links() !!}
          @else
            <div class="col-md-12 mt-4 text-center">
              <h3 class="text-danger"><i>Belum ada pembelian yang terselesaikan.</i></h3>
            </div>
          @endif
        </div>

        {{-- laporan pembelian --}}
        <div class="tab-pane fade" id="laporan" role="tabpanel" aria-labelledby="laporan-tab">
          @if (count($lp) > 0)
            @foreach ($lp as $data)
              <div class="_box-pembelian mb-5">
                <div class="_box-nopesanan d-block mx-auto">
                  <p class="text-center"><i class="mdi mdi-cart-outline pr-1"></i> No Pesanan <span>{{ number_format($data->no_pesanan, 0, '.', '.') }}</span></p>
                </div>
                <div class="row">
                  @foreach ($laporan as $result)
                    @if ($data->no_pesanan == $result->no_pesanan)
                      <?php
                      $total  = intval($result->jumlah_pesanan) * intval($result->harga);
                      if($result->status_pengiriman == 1){
                        $class  = 'badge-secondary';
                        $status = 'Belum Bayar';
                      }else if($result->status_pengiriman == 2){
                        $class  = 'badge-info';
                        $status = 'Sedang di proses';
                      }else if($result->status_pengiriman == 4){
                        $class  = 'badge-success';
                        $status = 'Selesai';
                      }else if($result->status_pengiriman == 5){
                        $class  = 'badge-danger';
                        $status = 'Batal';
                      }else if($result->status_pengiriman == 3){
                        $class  = 'badge-primary';
                        $status = 'Pengiriman';
                      }
                      ?>
                      <div class="col-12 col-md-6 mb-3">
                        <div class="_box-pembelian-produk">
                          <div class="row">
                            <div class="col-12 col-md-3">
                              <div class="_box-cover-foto">
                                <img src="{{ asset('/assets/images/product/'.$result->nama_foto) }}">
                              </div>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                <b>{{ $result->nama_produk }}</b>
                              </div>
                              <p>Jumlah : <b>{{ $result->jumlah_pesanan }}</b></p>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                Harga <p><b>{{ "Rp ".number_format($result->harga, 0, ',', '.') }}</b></p>
                              </div>
                              <p>Status <span class="badge badge-pill {{ $class }}">{{ $status }}</span></p>
                            </div>
                            <div class="col-6 col-md-3">
                              <div class="_pembelian-product-name">
                                Total Harga <p><b>{{ "Rp ".number_format($total, 0, ',', '.') }}</b></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endif
                  @endforeach
                </div>
              </div>
            @endforeach
            {!! $lp->links() !!}
          @else
            <div class="col-md-12 mt-4 text-center">
              <h3 class="text-danger"><i>Belum ada laporan pembelian.</i></h3>
            </div>
          @endif
        </div>
      </div>
    </nav>
  </div>
@endsection

@section('script')
  <script>
    $(document).ready(function(){
      $('.tgl').each(function(index){
        var endDate = $(this).data('end');
        // CountDown(endDate);
        var end = new Date(endDate);
        var _second = 1000;
        var _minute = _second * 60;
        var _hour = _minute * 60;
        var _day = _hour * 24;
        var timer;
        function showRemaining(a) {
          var now = new Date();
          var distance = end - now;
          if (distance < 0) {

            clearInterval(timer);
            $('.tgl').html('<div class="text-danger">Waktu pembayaran telah lewat, Pembayaran dibatalkan.</div>');
            return;
          }
          var days = Math.floor(distance / _day);
          var hours = Math.floor((distance % _day) / _hour);
          var minutes = Math.floor((distance % _hour) / _minute);
          var seconds = Math.floor((distance % _minute) / _second);

          var html = '<div class="text-danger"><b>' + days + 'd : ' + hours + 'h : ' + minutes + 'm : ' + seconds + 's</b></div><div style="font-size:11px;">Pembayaran berakhir.</div>';

          // $('.tgl').each(function(index){
            a.html(html);
          // })
        }
        var abc = $(this);
        timer = setInterval(showRemaining, 1000, abc);
      })
    });

    $(document).on('click', '.konfirmasi', function(e) {
      e.preventDefault();

      var url   = $(this).attr('href');
      var nama  = $(this).data('nama');

      $('#ModalHeader').html('Konfirmasi');
      $('#ModalContent').html('<p>Apakah produk / barang <b><i>' + nama + '</i></b> telah sampai?</p>');
      $('#ModalFooter').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Belum</button><a href="' + url + '" class="btn btn-primary">Ya sudah sampai.</a>');
      $('#ModalGue').modal('show');
    });

    $(document).on('click', '.ulasan', function(e){
      e.preventDefault();

      var url = $(this).attr('href');
      var html = '<form method="post" class="form-horizontal" action="' + url + '">';
      html += '{{ csrf_field() }}';
      html += '<div class="form-group">'; // form group 1
      html += '<label for="isi">Isi Ulasan</label>'; // label komentar
      html += '<textarea class="form-control" name="isi" id="isi" rows="5" cols="5" placeholder="Ulasan kamu pada produk ini" required></textarea>'; // komentar
      html += '<div class="invalid-feedback">Isi ulasan kamu pada produk ini.</div>';
      html += '</div>'; // end form group 1
      html += '<div class="form-group">'; // form group 2
      html += '<label for="isi">Rating Produk</label>'; // label komentar
      html += '<input class="rating rating-loading" name="rating" data-size="sm" data-min="0" data-max="5" data-step="0.5" required="required" />';
      html += '<div class="invalid-feedback">Masukan rating produk ini.</div>';
      html += '</div>'; // end form group 2
      html += '<button type="submit" class="btn btn-primary float-right">Simpan Ulasan</button>';
      html += '</form>';

      $('#ModalHeader').html('Ulasan');
      $('#ModalContent').html(html);
      $('#ModalFooter').html('<button type="button" data-dismiss="modal" class="btn btn-default">Batal</button>');
      $('#ModalGue').modal('show');

      $('.rating').rating();
    });
  </script>
@endsection