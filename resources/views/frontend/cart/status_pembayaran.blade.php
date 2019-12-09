@extends('layouts.frontend')
@section('content')
  <div class="container mt-5">
    <div class="col-md-6 mx-auto">
      <div class="_create-toko">
        <div id="_countdown" class="d-block text-center"></div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    $(document).ready(function(){
      var end = '{{ $pesanan->created_at->addDays(3) }}';
      CountDown(end);
    });

    function CountDown(end){
      var endDate = new Date(end);
      var _second = 1000;
      var _minute = _second * 60;
      var _hour = _minute * 60;
      var _day = _hour * 24;
      var timer;
      function showRemaining() {
        var now = new Date();
        var distance = endDate - now;
        if (distance < 0) {

          clearInterval(timer);
          document.getElementById('_countdown').innerHTML = '<h4>Waktu pembayaran telah lewat, Pembayaran dibatalkan.</h4> ';
          return;
        }
        var days = Math.floor(distance / _day);
        var hours = Math.floor((distance % _day) / _hour);
        var minutes = Math.floor((distance % _hour) / _minute);
        var seconds = Math.floor((distance % _minute) / _second);

        var html = '<div class="d-block text-center time">' + days + 'd : ' + hours + 'h : ' + minutes + 'm : ' + seconds + 's</div><div class="col-12 text-center"><h4>Sebelum pembayaran berakhir.</h4></div>';

        document.getElementById('_countdown').innerHTML = html;
      }
      timer = setInterval(showRemaining, 1000);
    }
  </script>
@endsection